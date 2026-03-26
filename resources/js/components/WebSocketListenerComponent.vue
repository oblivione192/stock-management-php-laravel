<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { onMounted, onBeforeMount, onBeforeUnmount, watch } from 'vue';
import loadEcho from '@/echo'; 
import Events from '@/events';
import useDashboardStore from '@/stores/dashboardStore';
import useProfileStore from '@/stores/profileStore';  

onBeforeMount(() => {
    loadEcho();
});

const profileStore = useProfileStore();
const dashboardStore = useDashboardStore();
const cleanupBindings = () => Events.leaveAllEvents();
const handlePageHide = () => {
    cleanupBindings();
};

const bindEvents = () => Events.listenToAllEvents();

onMounted(() => {
    const { isAuthenticated } = storeToRefs(profileStore);

    bindEvents();

    watch(isAuthenticated, (loggedIn, wasLoggedIn) => {
        if (!loggedIn && wasLoggedIn) {
            cleanupBindings();
            dashboardStore.clearDashboard();
        }
    });
});

window.addEventListener('pagehide', handlePageHide);
window.addEventListener('beforeunload', handlePageHide);

onBeforeUnmount(() => {
    window.removeEventListener('pagehide', handlePageHide);
    window.removeEventListener('beforeunload', handlePageHide);
    cleanupBindings();
});
</script>

<style scoped></style>
