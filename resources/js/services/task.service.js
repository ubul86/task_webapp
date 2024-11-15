import { publicApi } from "./api";

class TaskService {
    fetchTasks() {
        return publicApi
            .get("/tasks")
            .then((response) => response.data.data)
            .catch((error) => {
                console.error("Failed to fetch data:", error);
                throw error;
            });
    }

    store(item) {
        return publicApi.post(`/tasks`, item).then((response) => response.data.data)
            .catch((error) => {
                console.error("Failed when store the data:", error);
                throw error;
            });
    }

    update(item) {
        return publicApi.put(`/tasks/${item.id}`, item).then((response) => response.data.data)
            .catch((error) => {
                console.error("Failed when store the data:", error);
                throw error;
            });
    }

    setCompletedItem(id) {
        return publicApi.put(`/tasks/set-completed/${id}`).then((response) => response.data.data)
            .catch((error) => {
                console.error("Failed when set the data to complete:", error);
                throw error;
            });
    }
    deleteItem(id) {
        return publicApi
            .delete(`/tasks/${id}`)
            .then((response) => response.data.data)
            .catch((error) => {
                console.error("Failed to delete data:", error);
                throw error;
            });
    }

    bulkDelete(ids) {
        const idString = ids.join(",");
        return publicApi
            .delete("/tasks/bulk-destroy", {
                params: { ids: idString }
            })
            .then((response) => response.data.data)
            .catch((error) => {
                console.error("Failed to delete data:", error);
                throw error;
            });
    }

    bulkCompleted(ids) {
        const idString = ids.join(",");
        return publicApi
            .put("/tasks/set-bulk-completed", {
                 ids: idString
            })
            .then((response) => response.data.data)
            .catch((error) => {
                console.error("Failed to confirmed data:", error);
                throw error;
            });
    }
}

export default new TaskService();
