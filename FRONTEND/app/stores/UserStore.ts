import { defineStore } from "pinia";
import type { User } from "~/types/User";

export const useUserStore = defineStore("user", {
  state: () => ({
    users: [] as User[],
    currentUser: null,
    isLoading: false,
    errors: {} as ValidationErrors,
    isPersisting: false
  }),

  getters: {
    getUserById: (state) => (id: string) => {
      return state.users.find(user => user.id === id) || null;
    },

    getUsersByRole: (state) => (role: string) => {
      return state.users.filter(user => user.role === role);
    }
  },

  actions: {
    async findAll() {
      this.isLoading = true;
      try {
        const { data } = await useApi().get<User[]>(ApiUrl.USERS);
        this.users = data;
      } catch (error) {
        this.errors = useValidationErrors(error);
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    async findOne(id: string) {
      this.isLoading = true;
      try {
        const { data } = await useApi().get<User>(
          ApiUrl.parameterized(ApiUrl.USER_BY_ID, id)
        );
        return data;
      } catch (error) {
        this.errors = useValidationErrors(error);
        throw error;
      } finally {
        this.isLoading = false;
      }
    },

    // Crée un nouvel utilisateur
    async store(user: Partial<User>) {
      this.isPersisting = true;
      try {
        const { data } = await useApi().post<User>(
          ApiUrl.USERS,
          user
        );
        this.users.push(data);
        return data;
      } catch (error) {
        this.errors = useValidationErrors(error);
        throw error;
      } finally {
        this.isPersisting = false;
      }
    },

    // Met à jour un utilisateur existant
    async update(user: User) {
      this.isPersisting = true;
      try {
        const { data } = await useApi().put<User>(
          ApiUrl.parameterized(ApiUrl.USER_BY_ID, user.id),
          user
        );
        const index = this.users.findIndex(u => u.id === user.id);
        if (index !== -1) {
          this.users[index] = data;
        }
        return data;
      } catch (error) {
        this.errors = useValidationErrors(error);
        throw error;
      } finally {
        this.isPersisting = false;
      }
    },

    // Supprime un utilisateur
    async delete(id: string) {
      this.isPersisting = true;
      try {
        await useApi().del(
          ApiUrl.parameterized(ApiUrl.USER_BY_ID, id)
        );
        this.users = this.users.filter(user => user.id !== id);
      } catch (error) {
        this.errors = useValidationErrors(error);
        throw error;
      } finally {
        this.isPersisting = false;
      }
    },

    // Nettoyage du store
    cleanStorage() {
      this.users = [];
      this.currentUser = null;
      this.isLoading = false;
      this.errors = {};
      this.isPersisting = false;
    }
  }
});