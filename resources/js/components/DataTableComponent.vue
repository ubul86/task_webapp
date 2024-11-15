<template>

    <SelectedItemsCountedTimesComponent :items="selectedItems" v-if="selectedItems.length" class="mb-5" />

    <v-data-table
        v-model="selected"
        :headers="headers"
        show-select
        :items="taskStore.tasks"

    >
        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title>Tasks</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-spacer></v-spacer>
                <v-dialog v-model="dialog" max-width="500px">
                    <template v-slot:activator="{ props }">
                        <v-btn class="mb-2" color="primary" dark v-bind="props">New Task</v-btn>
                    </template>
                    <v-card>
                        <v-card-title>
                            <span class="text-h5">{{ formTitle }}</span>
                        </v-card-title>
                        <v-card-text>
                            <v-container>
                                <v-row>
                                    <v-col cols="12" >
                                        <v-text-field
                                            v-model="editedItem.description"
                                            label="Description"
                                            :error="!!formErrors.description"
                                            :error-messages="formErrors.description || []"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" >
                                        <v-select
                                            v-model="editedItem.user_id"
                                            :items="props.users"
                                            item-value="id"
                                            item-title="name"
                                            label="Users"
                                            :error="!!formErrors.user_id"
                                            :error-messages="formErrors.user_id || []"
                                        ></v-select>
                                    </v-col>
                                    <v-col cols="12" >
                                        <v-text-field
                                            v-model="editedItem.estimated_time"
                                            label="Estimated Time"
                                            :error="!!formErrors.estimated_time"
                                            :error-messages="formErrors.estimated_time || []"
                                        ></v-text-field>
                                    </v-col>
                                    <v-col cols="12" >
                                        <v-text-field
                                            v-model="editedItem.used_time"
                                            label="Used Time"
                                            :error="!!formErrors.used_time"
                                            :error-messages="formErrors.used_time || []"
                                        ></v-text-field>
                                    </v-col>
                                </v-row>
                            </v-container>
                        </v-card-text>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue-darken-1" variant="text" @click="close">Cancel</v-btn>
                            <v-btn color="blue-darken-1" variant="text" @click="save">Save</v-btn>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
                <v-dialog v-model="dialogDelete" max-width="500px">
                    <v-card>
                        <v-card-title class="text-h5">Are you sure you want to delete this task?</v-card-title>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue-darken-1" variant="text" @click="closeDelete">Cancel</v-btn>
                            <v-btn color="blue-darken-1" variant="text" @click="deleteItemConfirm">OK</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
                <v-dialog v-model="dialogCompleted" max-width="500px">
                    <v-card>
                        <v-card-title class="text-h5">Are you sure you set this task to complete?</v-card-title>
                        <v-card-actions>
                            <v-spacer></v-spacer>
                            <v-btn color="blue-darken-1" variant="text" @click="closeCompleted">Cancel</v-btn>
                            <v-btn color="blue-darken-1" variant="text" @click="setCompletedConfirm">OK</v-btn>
                            <v-spacer></v-spacer>
                        </v-card-actions>
                    </v-card>
                </v-dialog>
            </v-toolbar>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
            <v-icon class="me-2" size="small" @click="editItem(item)">mdi-pencil</v-icon>
            <v-icon class="me-2" size="small" @click="setCompletedItem(item)">mdi-check</v-icon>
            <v-icon size="small" @click="deleteItem(item)">mdi-delete</v-icon>
        </template>
        <template v-slot:no-data>
            <v-btn color="primary" @click="initialize">Reset</v-btn>
        </template>
        <template v-slot:[`item.is_completed`]="{ item }">
            <v-icon
                :color="item.is_completed ? 'green' : 'red'"
                small
            >
                {{ item.is_completed ? 'mdi-check' : 'mdi-close' }}
            </v-icon>
        </template>
        <template v-slot:[`item.used_time`]="{ item, value }">
            <v-chip :color="getUsedTimeColor(item)">
                {{ formatTime(value) }}
            </v-chip>
        </template>
        <template v-slot:[`item.estimated_time`]="{ value }">
            {{ formatTime(value) }}
        </template>
    </v-data-table>
    <v-row v-if="selected.length">
        <v-col>
            <v-btn @click="bulkDelete">Bulk delete</v-btn>
            <v-btn @click="bulkCompleted">Bulk confirmed</v-btn>
        </v-col>
    </v-row>
    <SelectedItemsCountedTimesComponent :items="selectedItems" v-if="selectedItems.length" class="mt-5" />
</template>

<script setup>
import { ref, reactive, computed, nextTick } from 'vue'
import { useTaskStore } from '@/stores/task.store.js';
import SelectedItemsCountedTimesComponent from '@/components/SelectedItemsCountedTimesComponent.vue'
import { useToast } from 'vue-toastification';
import { formatTime } from '@/utils/formatTime';
import useForm from '@/composables/useForm.js';

const dialog = ref(false)
const dialogDelete = ref(false)
const dialogCompleted = ref(false)
const toast = useToast()
const { formErrors, resetErrors, handleApiError } = useForm();

const taskStore = useTaskStore();

const headers = [
    {
        title: 'Task ID',
        align: 'start',
        key: 'id',
    },
    { title: 'User', key: 'user_name' },
    { title: 'Description', key: 'description' },
    { title: 'Estimated Time', key: 'estimated_time' },
    { title: 'Used Time', key: 'used_time' },
    { title: 'Is Completed', key: 'is_completed', align: 'center'},
    { title: 'Created At', key: 'created_at' },
    { title: 'Updated At', key: 'updated_at' },
    { title: 'Actions', key: 'actions', sortable: false },
]

const selected = ref([])

const editedIndex = ref(-1)
const editedItem = reactive({
    user_id: null,
    description: '',
    estimated_time: null,
    used_time: null,
})

const defaultItem = {
    user_id: null,
    description: '',
    estimated_time: null,
    used_time: null,
}

const formTitle = computed(() =>
    editedIndex.value === -1 ? 'New Task' : 'Edit Task'
)

const props = defineProps({
    users: {
        type: Array,
        required: false
    }
});

const selectedItems = computed(() => {
    return taskStore.tasks.filter(task => selected.value.includes(task.id));
});

const editItem = (item) => {
    editedIndex.value = taskStore.tasks.indexOf(item)
    Object.assign(editedItem, item)
    dialog.value = true
}

const setCompletedItem = (item) => {
    editedIndex.value = taskStore.tasks.indexOf(item)
    Object.assign(editedItem, item)
    dialogCompleted.value = true
}

const deleteItem = (item) => {
    editedIndex.value = taskStore.tasks.indexOf(item)
    Object.assign(editedItem, item)
    dialogDelete.value = true
}

const deleteItemConfirm = async () => {
    try {
        await taskStore.deleteItem(editedItem.id);
        closeDelete()
        toast.success('You are successfully deleted the item!');
    }
    catch(error) {
        toast.success(error.response.data.message);
    }
}

const setCompletedConfirm = async () => {
    try {
        await taskStore.setCompletedItem(editedItem.id)
        closeCompleted();
        toast.success('You are successfully set the item to completed!');
    }
    catch(error) {
        toast.success(error.response.data.message);
    }
}

const close = async () => {
    dialog.value = false
    await nextTick()
    Object.assign(editedItem, defaultItem)
    editedIndex.value = -1
}

const closeDelete = async () => {
    dialogDelete.value = false
    await nextTick()
    Object.assign(editedItem, defaultItem)
    editedIndex.value = -1
}

const closeCompleted = async () => {
    dialogCompleted.value = false
    await nextTick()
    Object.assign(editedItem, defaultItem)
    editedIndex.value = -1
}

const save = async () => {
    resetErrors();
    try {
        if (editedIndex.value > -1) {
            await taskStore.update(editedIndex.value, editedItem);
            toast.success('You are successfully edited the item!');
        } else {
            await taskStore.store(editedItem)
            toast.success('You are successfully created a new item!');
        }
        close()
    }
    catch(error) {
        handleApiError(error);
        toast.error(error.response.data.message);
    }
}

const getUsedTimeColor = (item) => {
    if (item.used_time / item.estimated_time > 0.9) {
        return 'red';
    }
    if (item.used_time / item.estimated_time > 0.6) {
        return 'orange';
    }
    return 'green'
}

const emptySelected = () => {
    selected.value = [];
}

const bulkDelete = async () => {
    try {
        await taskStore.bulkDelete(selected.value);
        emptySelected();
        toast.success('You are successfully delete all the selected items!');
    }
    catch(error) {
        toast.error(error.response.data.message)
    }
}

const bulkCompleted = async () => {
    try {
        await taskStore.bulkCompleted(selected.value);
        emptySelected();
        toast.success('You are successfully set all the selected items to completed!');
    }
    catch(error) {
        toast.error(error)
    }
}

</script>
