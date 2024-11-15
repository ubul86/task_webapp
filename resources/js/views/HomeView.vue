<script setup>
import { computed, onMounted } from 'vue';
import { useTaskStore } from '@/stores/task.store.js';
import { useUserStore} from '@/stores/user.store.js'
import DataTableComponent from '@/components/DataTableComponent.vue'

const taskStore = useTaskStore();
const userStore = useUserStore();

const users = computed( () => userStore.users);
onMounted(() => {
    taskStore.fetchTasks();
    userStore.fetchUsers();
});

</script>

<template>
    <DataTableComponent :users="users"></DataTableComponent>
</template>
