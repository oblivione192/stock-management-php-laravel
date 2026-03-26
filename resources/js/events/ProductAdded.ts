import { useEcho } from '@laravel/echo-vue';
import EventBus from '@/events/EventBus';
import type {ProductRow} from '@/Objects/Responses/ProductResponses';
import {Events, Channels} from './Events';

type EchoEventListener = {
    listen: () => void;
    leave: () => void;
    channel: () => {
        subscribed: (callback: () => void) => unknown;
        error: (callback: (error: unknown) => void) => unknown;
    };
};

let productAddedEvent: EchoEventListener | null = null;

const createProductAddedEvent = (): EchoEventListener => {
    const listener = useEcho(
        Channels.PRODUCTS,
        Events.PRODUCT_ADDED,
        (e: ProductRow) => {
            EventBus.emit(Events.PRODUCT_ADDED, e);
        },
    ) as EchoEventListener;

    listener
        .channel()
        .subscribed(() => {
            console.log('Subscribed to channel:', Channels.PRODUCTS);
        })
        .error((error: unknown) => {
            console.error('Channel subscription failed:', error);
        });

    return listener;
};

export default {
    listen: () => {
        if (!productAddedEvent) {
            productAddedEvent = createProductAddedEvent();
        }

        productAddedEvent.listen();
    },
    leave: () => {
        if (!productAddedEvent) {
            return;
        }

        productAddedEvent.leave();
        productAddedEvent = null;
    },
};
