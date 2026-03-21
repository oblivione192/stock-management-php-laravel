import { useEcho } from '@laravel/echo-vue';
import EventBus from '@/events/EventBus';
import type {ProductRow} from '@/Objects/Responses/ProductResponses';
import {Events, Channels} from './Events';

const productAddedEvent = useEcho(
    Channels.PRODUCT_ADDED, // important!
    Events.PRODUCT_ADDED,
    (e: ProductRow) => {
        EventBus.emit(Events.PRODUCT_ADDED,e);
    },
);

export default productAddedEvent;
