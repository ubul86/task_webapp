import { computed } from 'vue';

export function useCountedTimes(editedItem) {
    const estimatedHours = computed({
        get: () => Math.floor((editedItem.value.estimated_time || 0) / 3600),
        set: (value) => {
            const minutes = estimatedMinutes.value;
            editedItem.value.estimated_time = value * 3600 + minutes * 60;
        },
    });

    const estimatedMinutes = computed({
        get: () => Math.floor(((editedItem.value.estimated_time || 0) % 3600) / 60),
        set: (value) => {
            const hours = estimatedHours.value;
            editedItem.value.estimated_time = hours * 3600 + value * 60;
        },
    });

    const usedHours = computed({
        get: () => Math.floor((editedItem.value.used_time || 0) / 3600),
        set: (value) => {
            const minutes = usedMinutes.value;
            editedItem.value.used_time = value * 3600 + minutes * 60;
        },
    });

    const usedMinutes = computed({
        get: () => Math.floor(((editedItem.value.used_time || 0) % 3600) / 60),
        set: (value) => {
            const hours = usedHours.value;
            editedItem.value.used_time = hours * 3600 + value * 60;
        },
    });

    return {
        estimatedHours,
        estimatedMinutes,
        usedHours,
        usedMinutes,
    };
}
