<script setup lang="ts">
import ChartDataLabels from 'chartjs-plugin-datalabels'

import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  DoughnutController,
  ChartOptions
} from 'chart.js'

import { Doughnut } from 'vue-chartjs'

ChartJS.register(ChartDataLabels, Title, Tooltip, Legend, ArcElement, DoughnutController)

const props = defineProps<{
  chartData: any
  // chartOptions: any
}>()

const localChartOptions: ChartOptions<'doughnut'> = {
  plugins: {
    datalabels: {
      color: '#000',
      font: {
        weight: 'bold',
        size: 14
      },
      formatter: (value: number) => `$(value)` as any,
    },
    legend: {
      position: 'bottom'
    }
  }
}
</script>

<template>
  <Doughnut :data="chartData" :options="localChartOptions" />
</template>
