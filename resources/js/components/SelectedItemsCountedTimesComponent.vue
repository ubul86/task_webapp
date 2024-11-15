<template>
    <v-row>
        <v-col cols="6">
            <v-card class="time-card">
                <v-card-title>Estimated time of selected items</v-card-title>
                <v-card-text>
                    <p>{{ totalTimes.estimated }}</p>
                </v-card-text>
            </v-card>
        </v-col>

        <v-col cols="6">
            <v-card class="time-card">
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
});

const totalTimes = computed(() => {
    const totalEstimated = props.items.reduce((sum, item) => sum + item.estimated_time, 0);
    const totalUsed = props.items.reduce((sum, item) => sum + item.used_time, 0);

    return {
        estimated: formatTime(totalEstimated),
        used: formatTime(totalUsed),
    };
});
</script>
<style scoped>
.time-card {
    background-color: #f5f5f5;
    border: 1px solid #ddd;
    box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.1);
    padding: 16px;
}
</style>
