import mitt from 'mitt';
import type { ProductRow, ProductDeleteResult } from '@/Objects/Responses/ProductResponses';
import type { MovementRow } from '@/Objects/Responses/StockMovementResponses';
import useDashboardStore from '@/stores/dashboardStore';
import {Events} from './Events';

type EventsMap = {
    [Events.PRODUCT_ADDED]: ProductRow;
    [Events.PRODUCT_DELETED]: ProductDeleteResult;
    [Events.STOCK_IN]: MovementRow;
    [Events.STOCK_OUT]: MovementRow;
};

const eventBus =  mitt<EventsMap>();

eventBus.on(Events.PRODUCT_ADDED, (product:ProductRow): void  => {
    const dashboardStore = useDashboardStore();
    dashboardStore.handleProductAdded(product);
});

eventBus.on(Events.PRODUCT_DELETED, (productDeleteResult: ProductDeleteResult): void =>{
   const dashboardStore = useDashboardStore();
   dashboardStore.handleProductDelete(productDeleteResult);
});



export default eventBus;

