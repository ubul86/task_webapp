<script setup>

import { useTaskStore } from '@/stores/task.store.js';
import { useToast } from 'vue-toastification';
import { computed, ref, watch } from 'vue'

const toast = useToast();
const taskStore = useTaskStore();

const props = defineProps({
    isDialogCompletedOpen: {
        type: Boolean,
        required: true,
    },
    itemId: {
        type: Number,
        required: true,
    },
    isCompleted: {
        type: Boolean,
        required: true
    }
});

const emit = defineEmits(['update:isDialogCompletedOpen', 'closeCompleted']);

const localDialogOpen = ref(props.isDialogCompletedOpen);

watch(() => props.isDialogCompletedOpen, (newValue) => {
    localDialogOpen.value = newValue;
});

watch(localDialogOpen, (newValue) => {
    emit('update:isDialogCompletedOpen', newValue);
});

const dialogCompletedText = computed(() =>
    props.isCompleted
        ? 'Are you sure you want to set this task to incomplete?'
        : 'Are you sure you want to set this task to complete?'
);

const setCompleteItemConfirm = async () => {
    try {

        const newState = !props.isCompleted;
        await taskStore.toggleCompletedItem(props.itemId)

        toast.success(
            newState
                ? 'You successfully marked the task as completed!'
                : 'You successfully marked the task as incomplete!'
        );
        emit('closeCompleted');
    } catch (error) {
        toast.error(error.response.data.message);
    }
};

const cancelComplete = () => {
    emit('closeCompleted');
};
</script>

<template>

    <v-dialog v-model="localDialogOpen" max-width="500px">
        <v-card>
            <v-card-title class="text-h5">
                {{ dialogCompletedText }}
            </v-card-title>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="cancelComplete">Cancel</v-btn>
                <v-btn color="blue-darken-1" variant="text" @click="setCompleteItemConfirm">OK</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>
