import { publicApi } from "./api";

class UserService {
    getUser() {
        return publicApi
            .get("/user")
            .then((response) => response.data.data)
            .catch((error) => {
                console.error("Failed to fetch user data:", error);
                throw error;
            });
    }

    getUsers() {
        return publicApi
            .get("/users")
            .then((response) => response.data.data)
            .catch((error) => {
                console.error("Failed to fetch users:", error);
                throw error;
            });
    }
}

export default new UserService();
