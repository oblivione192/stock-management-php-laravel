import mitt from 'mitt';
import type { ProductRow } from '@/Objects/Responses/ProductResponses';
import useDashboardStore from '@/stores/dashboardStore'
import {Events} from './Events';

type EventsMap = {
    [Events.PRODUCT_ADDED]: ProductRow;
};

const eventBus =  mitt<EventsMap>();

eventBus.on(Events.PRODUCT_ADDED, (product:ProductRow): void  => {
    const dashboardStore = useDashboardStore();
    dashboardStore.handleProductAdded(product);
});


export default eventBus;

