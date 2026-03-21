<script setup lang="ts">
import { storeToRefs } from 'pinia';
import { onMounted, onBeforeUnmount, watch } from 'vue';
import Events from '@/events';
import useDashboardStore from '@/stores/dashboardStore';
import useProfileStore from '@/stores/profileStore';

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
