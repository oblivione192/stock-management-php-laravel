import {defineStore} from 'pinia';  

export const useProfileStore = defineStore('profile', {
    state: () => ({
        name: '',
        email: '',
    }),
    actions: {
        setProfile(name: string, email: string) {
            this.name = name;
            this.email = email;
        }, 
        clearProfile(){
            this.name = ''; 
            this.email = ''; 
        }
    },
    getters: {
        isAuthenticated: (state) => !!state.name && !!state.email,
    },
}); 

export default useProfileStore;


