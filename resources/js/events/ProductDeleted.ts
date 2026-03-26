import { useEcho } from '@laravel/echo-vue';
import EventBus from '@/events/EventBus';
import type { ProductDeleteResult } from '@/Objects/Responses/ProductResponses';
import { Events, Channels } from './Events';

type EchoEventListener = {
    listen: () => void;
    leave: () => void;
    channel: () => {
        subscribed: (callback: () => void) => {
            error: (callback: (error: unknown) => void) => unknown;
        };
        error: (callback: (error: unknown) => void) => unknown;
    };
};

let productDeletedEvent: EchoEventListener | null = null;

const createProductDeletedEvent = (): EchoEventListener => {
    const listener = useEcho(
        Channels.PRODUCTS,
        Events.PRODUCT_DELETED,
        (e: ProductDeleteResult) => {
            console.log(e);
            EventBus.emit(Events.PRODUCT_DELETED, e);
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
        if (!productDeletedEvent) {
            productDeletedEvent = createProductDeletedEvent();
        }

        productDeletedEvent.listen();
    },
    leave: () => {
        if (!productDeletedEvent) {
            return;
        }

        productDeletedEvent.leave();
        productDeletedEvent = null;
    },
};
