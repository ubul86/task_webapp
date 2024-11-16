import { defineStore } from 'pinia';
import taskService from '@/services/task.service.js';
export const useTaskStore = defineStore('task', {
    state: () => ({
        tasks: [],
    }),
    actions: {
        async fetchTasks() {
            try {
                this.tasks = await taskService.fetchTasks();
            } catch (error) {
                console.error('Error in fetching tasks:', error);
                throw error;
            }
        },

        async store(item) {
            try {
                const storedItem = await taskService.store(item);
                this.tasks.push(storedItem);
            } catch (error) {
                console.error('Error when store the item:', error);
                throw error;
            }
        },

        async update(index, item) {
            try {
                this.tasks[index] = await taskService.update(item);
            } catch (error) {
                console.error('Error when store the item:', error);
                throw error;
            }
        },

        async toggleCompletedItem(id) {
            try {
                const updatedTask = await taskService.toggleCompletedItem(id);
                const taskIndex = this.tasks.findIndex(task => task.id === id);
                if (taskIndex !== -1) {
                    this.tasks[taskIndex] = updatedTask;
                }
            } catch (error) {
                console.error('Error when set the item to confirmed:', error);
                throw error;
            }
        },

        async increaseUsedTime(id, time) {
            try {
                const updatedTask = await taskService.increaseUsedTime(id, time);
                const taskIndex = this.tasks.findIndex(task => task.id === id);
                if (taskIndex !== -1) {
                    this.tasks[taskIndex] = updatedTask;
                }
            } catch (error) {
                console.error('Error when set the item to confirmed:', error);
                throw error;
            }

        },

        async deleteItem(id) {
            try {
                await taskService.deleteItem(id);
                this.tasks = this.tasks.filter(task => task.id !== id);
            } catch (error) {
                console.error('Error in deleting tasks:', error);
                throw error;
            }
        },

        async bulkDelete(ids) {
            try {
                await taskService.bulkDelete(ids);
                this.tasks = this.tasks.filter(task => !ids.includes(task.id));
            }
            catch (error) {
                console.error('Error in deleting tasks:', error);
                throw error;
            }
        },

        async bulkCompleted(ids) {
            try {
                const completedAt = await taskService.bulkCompleted(ids);
                this.tasks = this.tasks.map(task => {
                    if (ids.includes(task.id)) {
                        return { ...task, completed_at: completedAt, is_completed: true };
                    }
                    return task;
                });
            }
            catch (error) {
                console.error('Error in confirmed tasks:', error);
                throw error;
            }
        }
    },
});
