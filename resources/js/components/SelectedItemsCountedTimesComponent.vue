<template>
    <v-row>
        <v-col cols="6">
            <v-card class="bg-grey-lighten-4 pt-5 pb-5 pl-5 pr-5">
                <v-card-title>Estimated time of selected items</v-card-title>
                <v-card-text>
                    <p>{{ totalTimes.estimated }}</p>
                </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="6">
            <v-card class="bg-grey-lighten-4 pt-5 pb-5 pl-5 pr-5">
                <v-card-title>Used time of selected items</v-card-title>
                <v-card-text>
                    <p>{{ totalTimes.used }}</p>
                </v-card-text>
            </v-card>
        </v-col>
    </v-row>
</template>

<script setup>
import { computed } from 'vue';
import { formatTime } from '@/utils/formatTime';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
    sortedAndFilteredItems: {
        type: Array,
        required: true
    }
});

const filteredTasks = computed(() => {

    if (!props.items || !props.sortedAndFilteredItems) {
        return [];
    }

     return  props.items.filter(item =>
            props.sortedAndFilteredItems.some(filteredItem => filteredItem.id === item.id)
        )
    }
)


const totalTimes = computed(() => {
    const totalEstimated = filteredTasks.value.reduce((sum, item) => sum + item.estimated_time, 0);
    const totalUsed = filteredTasks.value.reduce((sum, item) => sum + item.used_time, 0);

    return {
        estimated: formatTime(totalEstimated),
        used: formatTime(totalUsed),
    };
});
</script>
