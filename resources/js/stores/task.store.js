import { defineStore } from 'pinia';
import taskService from '@/services/task.service.js';
export const useTaskStore = defineStore('task', {
    state: () => ({
        tasks: [],
        meta: {
            'items_per_page' : 10,
            'total_items': 0,
            'total_pages': 0,
            'current_page': 1
        }
    }),
    actions: {
        async fetchTasks(params) {
            const data = await taskService.fetchTasks(params);
            this.tasks = data.tasks;
            this.meta = data.meta;
        },

        async store(item) {
            const storedItem = await taskService.store(item);
            this.tasks.push(storedItem);
        },

        async update(index, item) {
            this.tasks[index] = await taskService.update(item);
        },

        async toggleCompletedItem(id) {
            const updatedTask = await taskService.toggleCompletedItem(id);
            const taskIndex = this.tasks.findIndex(task => task.id === id);
            if (taskIndex !== -1) {
                this.tasks[taskIndex] = updatedTask;
            }
        },

        async increaseUsedTime(id, time) {
            const updatedTask = await taskService.increaseUsedTime(id, time);
            const taskIndex = this.tasks.findIndex(task => task.id === id);
            if (taskIndex !== -1) {
                this.tasks[taskIndex] = updatedTask;
            }
        },

        async deleteItem(id) {
            await taskService.deleteItem(id);
            this.tasks = this.tasks.filter(task => task.id !== id);
        },

        async bulkDelete(ids) {
            await taskService.bulkDelete(ids);
            this.tasks = this.tasks.filter(task => !ids.includes(task.id));
        },

        async bulkCompleted(ids) {
            const completedAt = await taskService.bulkCompleted(ids);
            this.tasks = this.tasks.map(task => {
                if (ids.includes(task.id)) {
                    return { ...task, completed_at: completedAt, is_completed: true };
                }
                return task;
            });
        }
    },
});
