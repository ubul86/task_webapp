<template>
    <DialogForm
        :title="title"
        :fields="fields"
        v-model:formData="editedItem"
        :dialog-visible="localDialogVisible"
        @cancel="handleCancel"
        @submit="handleSubmit"
    />
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import DialogForm from './DialogForm.vue';
import { useTaskStore } from '@/stores/task.store.js';
import { useToast } from 'vue-toastification';

const taskStore = useTaskStore();

const toast = useToast()

const props = defineProps({
    dialogVisible: Boolean,
    editedIndex: Number,
});

const localDialogVisible = ref(props.dialogVisible);

const emit = defineEmits(['update:dialogUsedTimeVisible', 'close']);

const title = ref('Add Used Time');

const editedItem = ref({
    id: null,
    used_time: null,
})

const defaultItem = {
    id: null,
    used_time: null,
}

watch(
    () => props.dialogVisible,
    (newVal) => {
        localDialogVisible.value = newVal;
    }
);

watch(
    () => props.editedIndex,
    (newVal) => {

        editedItem.value = {
            ...defaultItem,
        };

        if (newVal >= 0) {
            const task = taskStore.tasks[newVal];

            if (task) {
                editedItem.value = {
                    ...task,
                    usedHours: 0,
                    usedMinutes: 0,
                };
            }
        }
    }
);

const handleCancel = () => {
    localDialogVisible.value = false;
    editedItem.value = { ...defaultItem };
    emit('close');
};

const handleSubmit = async (itemToSubmit) => {
    const used_time = itemToSubmit.usedHours ? itemToSubmit.usedHours * 3600 + itemToSubmit.usedMinutes * 60 : null;

    try {
        await taskStore.increaseUsedTime(editedItem.value.id, used_time)
        localDialogVisible.value = false;
        editedItem.value = { ...defaultItem };
        emit('close');
    }
    catch(error) {
        toast.error(error.response.data.message);
    }

};


const fields = computed(() => [
    [
        { model: 'usedHours', component: 'v-text-field', props: { label: 'Used Hours', type: 'number'}, 'cols': 6 },
        { model: 'usedMinutes', component: 'v-text-field', props: { label: 'Used Minutes', type: 'number'}, 'cols': 6 },
    ],
]);

</script>
