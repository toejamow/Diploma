<script setup lang="ts">
import { ref, onMounted, computed } from 'vue'
import axios from '@/axios'
import { Doughnut } from 'vue-chartjs'

import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    ArcElement,
    DoughnutController,
} from 'chart.js'
import ChartDataLabels from 'chartjs-plugin-datalabels'
import AppButton from '@/components/ui/AppButton.vue'

ChartJS.register(Title, Tooltip, Legend, ArcElement, DoughnutController, ChartDataLabels)

import { useNotesStore } from '@/stores/notes'

const notesStore = useNotesStore()

const statistics = ref<any>(null)
const from = ref<string>('')
const to = ref<string>('')
const loading = ref(false)
const error = ref('')
const formatDateRu = (dateStr: string): string => {
    return new Date(dateStr).toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}
const formatLocalDate = (date: Date): string => {
    const year = date.getFullYear()
    const month = (date.getMonth() + 1).toString().padStart(2, '0')
    const day = date.getDate().toString().padStart(2, '0')
    return `${year}-${month}-${day}`
}

const formattedFrom = computed(() => formatDateRu(from.value))
const formattedTo = computed(() => formatDateRu(to.value))

const fetchStatistics = async () => {
    loading.value = true
    error.value = ''

    try {
        const response = await axios.get('/statistics', {
            params: { from: from.value, to: to.value }
        })
        statistics.value = response.data
    } catch (err) {
        error.value = 'Ошибка при загрузке статистики'
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    const today = new Date()
    const weekAgo = new Date()
    weekAgo.setDate(today.getDate() - 7)

    from.value = formatLocalDate(weekAgo)
    to.value = formatLocalDate(today)

    fetchStatistics()
})

const statusLabels = ['Новая', 'Выполнена', 'В процессе', 'Просрочена']
const statusColors = ['#f0f0f0', '#22c55e', '#3b82f6', '#f59e0b']

import type { ChartData } from 'chart.js'

const chartData = computed<ChartData<'doughnut'>>(() => ({
    labels: statusLabels,
    datasets: [
        {
            data: statistics.value ? Object.values(statistics.value.status_counts) : [],
            backgroundColor: statusColors,
            borderWidth: 5
        }
    ]
}))

// const chartOptions = {
//     plugins: {
//         datalabels: {
//             color: '#000',
//             font: { weight: 'bold', size: 14 },
//             formatter: (value: number) => value.toString()
//         },
//         legend: {
//             position: 'bottom',
//             labels: {
//                 font: {
//                     size: 14
//                 }
//             }
//         }
//     },
//     maintainAspectRatio: false,
// }
</script>

<template>
    <div class="min-h-screen bg-neutral-50 p-6">
        <div class="max-w-4xl mx-auto bg-white shadow rounded-lg p-6">
            <div class="flex justify-between">

                <h1 class="text-2xl font-bold mb-4 text-neutral-800">Статистика за период</h1>
                <AppButton variant="secondary" size="sm" :loading="notesStore.isDownloading" @click="notesStore.downloadPDF"
                >
                Скачать статистику
            </AppButton>
        </div>

            <div class="flex flex-col items-center md:flex-row gap-4 mb-6">
                <div class="flex items-center gap-3">
                    <label for="from" class="text-sm text-neutral-700">От</label>
                    <input v-model="from" type="date" id="from"
                        class="border border-neutral-300 rounded-lg px-3 py-2 focus:ring focus:ring-primary-300" />
                </div>

                <div class="flex items-center gap-3">
                    <label for="to" class="text-sm text-neutral-700">До</label>
                    <input v-model="to" type="date" id="to"
                        class="border border-neutral-300 rounded-lg px-3 py-2 focus:ring focus:ring-primary-300" />
                </div>

                <AppButton variant="primary" size="sm" @click="fetchStatistics">
                    Обновить
                </AppButton>
            </div>

            <div v-if="loading" class="text-center py-10 text-neutral-500">Загрузка...</div>
            <div v-if="error" class="text-center text-red-500">{{ error }}</div>

            <div v-if="statistics && !loading" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-neutral-50 rounded p-4 shadow-sm">
                    <h2 class="text-lg font-semibold mb-4">По статусам</h2>
                    <div class="relative h-80">
                        <Doughnut :data="chartData" />
                    </div>
                </div>

                <div class="bg-neutral-50 rounded p-4 shadow-sm">
                    <h2 class="text-lg font-semibold mb-4">Статистика в цифрах</h2>
                    <ul class="text-sm space-y-2">
                        <li><strong>Заметок:</strong> {{ statistics.note_count }}</li>
                        <li><strong>Задач:</strong> {{ statistics.goal_count }}</li>
                        <li><strong>Выполнено задач:</strong> {{ statistics.completed_goals }}</li>
                        <li><strong>Процент выполнения заметок:</strong> {{ statistics.completion_percentage }}%</li>
                        <li><strong>Самая старая заметка:</strong> {{ statistics.oldest_note_date }}</li>
                        <li><strong>Самая новая заметка:</strong> {{ statistics.newest_note_date }}</li>
                        <li>
                            <strong>Период:</strong> {{ formattedFrom }} – {{ formattedTo }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
canvas {
    max-width: 100% !important;
}
</style>
