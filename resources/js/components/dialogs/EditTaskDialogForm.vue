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
import useForm from '@/composables/useForm.js';
import { useTaskStore } from '@/stores/task.store.js';
import { useToast } from 'vue-toastification';
import { useCountedTimes } from '@/composables/useCountedTimes.js';

const { formErrors, resetErrors, handleApiError } = useForm();

const taskStore = useTaskStore();

const toast = useToast()

const props = defineProps({
    dialogVisible: Boolean,
    editedIndex: Number,
    users: Array,
});

const localDialogVisible = ref(props.dialogVisible);

const emit = defineEmits(['update:dialogVisible', 'save', 'close']);

const title = ref('New Task');

const userItems = computed(() =>
    props.users.map(user => ({ id: user.id, name: user.name }))
);

const editedItem = ref({
    user_id: null,
    description: '',
    estimated_time: null,
    used_time: null,
})

const { estimatedHours, estimatedMinutes, usedHours, usedMinutes } = useCountedTimes(editedItem);

const defaultItem = {
    user_id: null,
    description: '',
    estimated_time: null,
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
        title.value = newVal < 0 ? 'New Task' : 'Edit Task';

        editedItem.value = {
            ...defaultItem,
            estimatedHours,
            estimatedMinutes,
            usedHours,
            usedMinutes
        };

        if (newVal >= 0) {
            const task = taskStore.tasks[newVal];

            if (task) {
                editedItem.value = {
                    ...task,
                    estimatedHours,
                    estimatedMinutes,
                    usedHours,
                    usedMinutes
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
    resetErrors();

    itemToSubmit.estimated_time = itemToSubmit.estimatedHours ? itemToSubmit.estimatedHours * 3600 + itemToSubmit.estimatedMinutes * 60 : null;
    itemToSubmit.used_time = itemToSubmit.usedHours ? itemToSubmit.usedHours * 3600 + itemToSubmit.usedMinutes * 60 : null;

    try {
        if (props.editedIndex.value > -1) {
            await taskStore.update(props.editedIndex.value, itemToSubmit);
            toast.success('You have successfully edited the item!');
        } else {
            await taskStore.store(itemToSubmit)
            toast.success('You have successfully created a new item!');
        }
        localDialogVisible.value = false;
        editedItem.value = { ...defaultItem };
        emit('close');
    }
    catch(error) {
        handleApiError(error);
        toast.error(error.response.data.message);
    }

};


const fields = computed(() => [
    { model: 'description', component: 'v-text-field', props: { label: 'Description', error: !!formErrors.value.description, 'error-messages': formErrors.value.description || [] } },
    { model: 'user_id', component: 'v-select', props: { label: 'Users', items: userItems.value, 'item-value': 'id', 'item-title': 'name', error: !!formErrors.value.user_id, 'error-messages': formErrors.value.user_id || [] } },
    [
        { model: 'estimatedHours', component: 'v-text-field', props: { label: 'Estimated Hours', type: 'number', 'hide-details': true, error: !!formErrors.value.estimated_time  }, 'cols': 6 },
        { model: 'estimatedMinutes', component: 'v-text-field', props: { label: 'Estimated Minutes', type: 'number', 'hide-details': true, error: !!formErrors.value.estimated_time }, 'cols': 6 },
        formErrors.value.estimated_time ? { component: 'v-alert', props: { text: formErrors.value.estimated_time ? formErrors.value.estimated_time[0] : '', color: 'error' }, 'cols': 12 } : {},
    ],
    [
        { model: 'usedHours', component: 'v-text-field', props: { label: 'Used Hours', type: 'number'}, 'cols': 6 },
        { model: 'usedMinutes', component: 'v-text-field', props: { label: 'Used Minutes', type: 'number'}, 'cols': 6 },
    ],
]);

</script>
