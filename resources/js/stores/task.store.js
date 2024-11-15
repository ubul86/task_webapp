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
                this.tasks.push(...storedItem);
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

        async setCompletedItem(id) {
            try {
                await taskService.setCompletedItem(id);
            } catch (error) {
                console.error('Error when set the item to confirmed:', error);
                throw error;
            }
        },

        async deleteItem(id) {
            try {
                await taskService.deleteItem(id);
            } catch (error) {
                console.error('Error in deleting tasks:', error);
                throw error;
            }
        },

        async bulkDelete(ids) {
            try {
                await taskService.bulkDelete(ids);
            }
            catch (error) {
                console.error('Error in deleting tasks:', error);
                throw error;
            }
        },

        async bulkCompleted(ids) {
            try {
                await taskService.bulkCompleted(ids);
            }
            catch (error) {
                console.error('Error in confirmed tasks:', error);
                throw error;
            }
        }
    },
});
