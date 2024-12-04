<template>

    <ToggleHeaderComponent :selectedHeaders="toggleHeaders" :headers="headers" @update:selectedHeaders="toggleHeaders = $event" />

    <SelectedItemsCountedTimesComponent :items="selectedItems" :sortedAndFilteredItems="sortedAndFilteredItems" v-if="selectedItems.length" class="mb-5" />

    <EditTaskDialogForm :edited-index="editedIndex" :dialog-visible="dialog" @close="close" :users="users" />

    <IncreaseUsedTimeDialogForm :edited-index="editedIndex" :dialog-visible="dialogIncreasedUsedTime" @close="closeIncreasedUsedTime" />

    <v-dialog v-model="dialogBulk" max-width="500px">
        <v-card>
            <v-card-title class="text-h5">{{ dialogBulkText }}</v-card-title>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="closeDialogBulk">Cancel</v-btn>
                <v-btn color="blue-darken-1" variant="text" @click="saveDialogBulk">OK</v-btn>
                <v-spacer></v-spacer>
            </v-card-actions>
        </v-card>
    </v-dialog>


    <v-data-table
        v-model="selected"
        :headers="computedHeaders"
        show-select
        :items="sortedAndFilteredItems"
        v-model:search="search"
        :filter-keys="['description', 'user_name']"
        :mobile="smAndDown"

    >
        <template v-slot:top>
            <v-toolbar flat>
                <v-toolbar-title>Tasks</v-toolbar-title>
                <v-divider class="mx-4" inset vertical></v-divider>
                <v-checkbox
                    v-model="filterIsCompleted"
                    color="green"
                    label="Complete Tasks"
                    :value="true"
                    hide-details
                ></v-checkbox>

                <v-checkbox
                    v-model="filterIsCompleted"
                    color="red"
                    label="Incomplete Tasks"
                    :value="false"
                    hide-details
                ></v-checkbox>
                <v-spacer></v-spacer>
                <v-text-field
                    v-model="search"
                    density="compact"
                    label="Search"
                    prepend-inner-icon="mdi-magnify"
                    variant="solo-filled"
                    flat
                    hide-details
                    single-line
                ></v-text-field>

                <v-btn color="primary" dark v-bind="props" @click="openDialog">New Task</v-btn>

            </v-toolbar>
        </template>
        <template v-slot:[`item.actions`]="{ item }">
            <v-icon class="me-2" size="small" @click="editItem(item)">mdi-pencil</v-icon>
            <v-icon
                class="me-2"
                size="small"
                @click="openToggleCompletedDialog(item)">
                {{ item.is_completed ? 'mdi-close' : 'mdi-check' }}
            </v-icon>
            <v-icon size="small" @click="dialogDelete(item, index)">mdi-delete</v-icon>
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
            <div class="used-time-container d-flex flex-column mt-2 mb-2">
                <v-chip :color="getUsedTimeColor(item)">
                    {{ formatTime(value) }}
                </v-chip>
                <v-btn
                    color="primary"
                    size="x-small"
                    class="justify-center align-center flex mt-2 mb-2"
                    v-if="!item.is_completed"
                    @click="openUsedTimeDialog(item)"
                >Add Time</v-btn>
            </div>
        </template>
        <template v-slot:[`item.estimated_time`]="{ value }">
            {{ formatTime(value) }}
        </template>
        <template v-slot:[`header.user_name`]="{}" v-if="!isMobileView">
            <div class="username-header d-flex justify-center align-center">
                <div style="flex: 4">
                    <v-autocomplete
                        v-model="nameSearch"
                        :items="props.users"
                        item-value="id"
                        item-title="name"
                        label="Users"
                        type="text"
                        hide-details
                        clearable
                    ></v-autocomplete>
                </div>
                <div style="flex: 1">
                    <v-icon
                        @click="toggleSort('user_name')"
                    >
                        mdi-sort
                    </v-icon>
                </div>
            </div>
        </template>
    </v-data-table>

    <v-row v-if="selected.length">
        <v-col>
            <v-btn @click="bulkDelete">Bulk delete</v-btn>
            <v-btn @click="bulkCompleted">Bulk confirmed</v-btn>
        </v-col>
    </v-row>

    <SelectedItemsCountedTimesComponent :items="selectedItems" v-if="selectedItems.length" class="mt-5" />

    <DialogDeleteComponent
        :is-dialog-delete-open="isDialogDeleteOpen"
        @update:isDialogDeleteOpen="isDialogDeleteOpen = $event"
        :item-id="editedItem?.id"
        @closeDelete="closeDelete"
    />

    <DialogCompletedComponent :is-dialog-completed-open="isDialogCompletedOpen"
                              @update:isDialogCompletedOpen="isDialogCompletedOpen = $event"
                              :item-id="editedItem?.id"
                              :isCompleted="editedItem?.is_completed"
                              @closeCompleted="closeCompleted"
                              />

</template>

<script setup>
import { ref, reactive, computed, nextTick, onMounted, onBeforeUnmount } from 'vue'
import { useTaskStore } from '@/stores/task.store.js';
import SelectedItemsCountedTimesComponent from '@/components/SelectedItemsCountedTimesComponent.vue'
import { useToast } from 'vue-toastification';
import { formatTime } from '@/utils/formatTime';

import DialogDeleteComponent from '@/components/dialogs/DialogDeleteComponent.vue'
import DialogCompletedComponent from '@/components/dialogs/DialogCompletedComponent.vue'
import ToggleHeaderComponent from '@/components/ToggleHeaderComponent.vue'
import { useDisplay } from 'vuetify'
import EditTaskDialogForm from '@/components/dialogs/EditTaskDialogForm.vue'
import IncreaseUsedTimeDialogForm from '@/components/dialogs/IncreaseUsedTimeDialogForm.vue'

const isMobileView = ref(window.innerWidth < 960);

const checkScreenSize = () => {
    isMobileView.value = window.innerWidth < 960;
};

onMounted(() => {
    window.addEventListener('resize', checkScreenSize);
});

onBeforeUnmount(() => {
    window.removeEventListener('resize', checkScreenSize);
});

const { smAndDown } = useDisplay()

const dialog = ref(false)

const dialogBulk = ref(false);
const dialogBulkType = ref("");

const dialogBulkText = computed(() =>
    dialogBulkType.value == 'delete' ? 'Are you sure you want to delete all the selected items?' : 'Are you sure you want to mark all the selected items as completed?'
);

const dialogIncreasedUsedTime = ref(false);

const toast = useToast()

const taskStore = useTaskStore();

const headers = [
    {
        title: 'Task ID',
        align: 'start',
        key: 'id',
    },
    { title: 'User', key: 'user_name', sortable: false },
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

const search = ref('');

const nameSearch = ref(null);

const sortBy = ref(null);
const sortDesc = ref(false);

const filterIsCompleted = ref([true, false]);

const toggleHeaders = ref(['id','user_name', 'description', 'estimated_time', 'used_time', 'is_completed', 'created_at', 'updated_at', 'actions']);

const computedHeaders = computed(() => {
    return headers.filter(header => toggleHeaders.value.includes(header.key));
})

const toggleSort = (key) => {
    if (sortBy.value === key) {
        sortDesc.value = !sortDesc.value;
    } else {
        sortBy.value = key;
        sortDesc.value = false;
    }
};

const filteredItems = computed(() => {
    let filtered = taskStore.tasks;

    if (nameSearch.value) {
        filtered = filtered.filter((task) => task.user_id === nameSearch.value);
    }

    if (filterIsCompleted.value.length > 0) {
        filtered = filtered.filter((task) => filterIsCompleted.value.includes(task.is_completed));
    }

    return filtered;
});

const sortedAndFilteredItems = computed(() => {
    const filtered = filteredItems.value;
    if (!sortBy.value) return filtered;

    return [...filtered].sort((a, b) => {
        const aValue = a[sortBy.value];
        const bValue = b[sortBy.value];

        if (aValue < bValue) return sortDesc.value ? 1 : -1;
        if (aValue > bValue) return sortDesc.value ? -1 : 1;
        return 0;
    });
});

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
    dialog.value = true
}

const openDialog = () => {
    dialog.value = true;
}

const openUsedTimeDialog = (item) => {
    editedIndex.value = taskStore.tasks.indexOf(item);
    dialogIncreasedUsedTime.value = true;
}

const close = async () => {
    dialog.value = false
    editedIndex.value = -1
}

const closeIncreasedUsedTime = async () => {
    dialogIncreasedUsedTime.value = false
    editedIndex.value = -1
}


const closeDialogBulk = async () => {
    dialogBulkType.value = '';
    dialogBulk.value = false;
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

const bulkDelete = () => {
    dialogBulkType.value = "delete";
    dialogBulk.value = true;
};

const bulkCompleted = () => {
    dialogBulkType.value = "complete";
    dialogBulk.value = true;
};

const saveDialogBulk = async () => {
    try {
        if (dialogBulkType.value === "delete") {
            await taskStore.bulkDelete(selected.value);
            toast.success('You have successfully deleted all the selected items!');
        } else if (dialogBulkType.value === "complete") {
            await taskStore.bulkCompleted(selected.value);
            toast.success('You have successfully set all the selected items to completed!');
        }
        emptySelected();
        closeDialogBulk();
    }
    catch(error) {
        toast.error(error.response.data.message)
    }
}


const isDialogDeleteOpen = ref(false);

const dialogDelete = (item) => {
    isDialogDeleteOpen.value = true;
    editedIndex.value = taskStore.tasks.indexOf(item)
    Object.assign(editedItem, item)
};

const closeDelete = async() => {
    isDialogDeleteOpen.value = false;
    await nextTick();
    Object.assign(editedItem, defaultItem)
    editedIndex.value = -1
};


const isDialogCompletedOpen = ref(false);

const openToggleCompletedDialog = (item) => {
    isDialogCompletedOpen.value = true;
    editedIndex.value = taskStore.tasks.indexOf(item)
    Object.assign(editedItem, item)
}

const closeCompleted = async () => {
    isDialogCompletedOpen.value = false
    await nextTick()
    Object.assign(editedItem, defaultItem)
    editedIndex.value = -1
}

</script>

<style>

.v-data-table__tr:nth-child(odd) {
    background-color: #e5e7eb;
}

.v-data-table__tr:nth-child(even) {
    background-color: #ffffff;
}

.used-time-container {
    align-items: center;
}

@media (max-width: 960px) {
    .used-time-container {
        align-items: flex-end;
    }
}

</style>
