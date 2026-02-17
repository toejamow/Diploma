import { defineStore } from 'pinia'
import axios from '@/axios'
import { PublicProps } from 'vue';

export interface Goal {
  id: number;
  description: string;
  is_completed: boolean;
}

interface Category {
  id: number
  name: string
}

export interface Task {
  id: number
  user_id: number
  title: string
  deadline: string //removed ? right after word deadline
  goals: Goal[]
  created_at: string
  updated_at: string
  status: string
  public_token: string
  category?: Category | null
  user?: PublicUser[]
}

interface PublicUser {
  // id: number this should be changed in laravel controller as well
  name: string
  email: string
}

export const useNotesStore = defineStore('tasks', {
  state: () => ({
    tasks: [] as Task[],
    currentTask: null as Task | null,
    isLoading: false,
    error: null as string | null,
    totalTasksCount: 0,
    isDownloading: false
  }),

  actions: {
    async fetchNotes({ status = '', sortBy = 'created_at', sortOrder = 'asc', search = '', category = '' } = {}) {
  this.isLoading = true
  this.error = null
  try {
    const response = await axios.get('/tasks', {
      params: { status, sort_by: sortBy, sort_order: sortOrder, search, category }
    })
    this.tasks = response.data.data
    this.totalTasksCount = response.data.total
  } catch (error: any) {
    console.error('Fetch tasks failed:', error)
    this.error = error.response?.data?.message || 'Не удалось загрузить заметки'
  } finally {
    this.isLoading = false
  }
},

    async fetchNoteById(id: string) {
      try {
        const response = await axios.get(`/tasks/${id}`)
        const note = response.data.data

        const existing = this.tasks.find(t => t.id === Number(note.id))
        if (existing) {
          Object.assign(existing, note)
        } else {
          this.tasks.push(note)
        }

        return note
      } catch (error) {
        console.error('Ошибка при загрузке заметки:', error)
        return null
      }
    },
    async createTask(taskData: { title: string;
       deadline?: string;
        goals: Goal[];
         category_id?: number; }) {
      if (taskData.deadline) {
        taskData.deadline = taskData.deadline.split('T')[0]
      }

      const payload = {
        title: taskData.title,
        deadline: taskData.deadline,
        goals: taskData.goals,
        category_id: 0
      }

      if (taskData.category_id) {
    payload.category_id = taskData.category_id
  }

      this.error = null
      try {
        const response = await axios.post('/create-task', payload)
        this.tasks.unshift(response.data.task)
        return true
      } catch (error: any) {
        console.error('Create task failed:', error)

        // Возвращаем ошибки обратно в компонент
        return {
          message: error.response?.data?.message || 'Ошибка при создании',
          errors: error.response?.data?.errors || {}
        }
      }
    },
    async updateTask(id: number, taskData: { title: string; deadline: string; goals: Goal[];
      category_id?: number;
     }, originalGoalIds?: number[]) {
      this.error = null

      try {
        const response = await axios.put(`/update-task/${id}`, taskData)

        if (response.data && response.data.task) {
          const index = this.tasks.findIndex(task => task.id === id)
          if (index !== -1) {
            this.tasks[index] = {
              ...this.tasks[index],
              title: response.data.task.title,
              deadline: response.data.task.deadline,
              goals: response.data.task.goals
            }
          }

          // Удаление задач, которых больше нет
          if (originalGoalIds) {
            const updatedGoalIds = taskData.goals.map(g => g.id).filter(id => typeof id === 'number') as number[]
            const goalsToDelete = originalGoalIds.filter(id => !updatedGoalIds.includes(id))

            for (const goalId of goalsToDelete) {
              try {
                await axios.delete(`/delete-goal/${goalId}`)
              } catch (deleteError) {
                console.error(`Не удалось удалить цель ${goalId}`, deleteError)
              }
            }
          }

          return true
        }

        // Если по каким-то причинам task не вернулся
        return {
          message: 'Нет данных о задаче',
          errors: {}
        }

      } catch (error: any) {
        console.error('Update task failed:', error)

        return {
          message: error.response?.data?.message || 'Ошибка при обновлении',
          errors: error.response?.data?.errors || {}
        }
      }
    }
    ,
    async deleteTask(id: number) {
      this.error = null
      try {
        await axios.delete(`/delete-task/${id}`)
        this.tasks = this.tasks.filter(task => task.id !== id)
      } catch (error: any) {
        console.error('Delete task failed:', error)
        this.error = error.response?.data?.message || 'Не удалось удалить заметку'
      }
    },
    async generateTasks(goal: string): Promise<string[]> {
      try {
        const response = await axios.post('/generate-tasks', { goal })
        return response.data.tasks || []
      } catch (error) {
        console.error('Ошибка при генерации задач:', error)
        return []
      }
    },
    async downloadPDF() {
      this.isDownloading = true

      try {
        const response = await axios.get('/download-stats', {
          responseType: 'blob', // Указываем, что ожидаем файл
        });
        const blob = new Blob([response.data], { type: 'application/pdf' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'statistics.pdf'; // Название файла
        link.click();
      } catch (error) {
        console.error('Ошибка при скачивании PDF:', error);
      } finally {
        this.isDownloading = false

      }
    },
    setCurrentNote(task: Task | null) {
      this.currentTask = task
    },

    getNoteById(id: number | string) {
      return this.tasks.find(task => task.id === Number(id)) || null
    }

  }
})