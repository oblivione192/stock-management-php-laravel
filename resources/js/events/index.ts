import productAddedEvent from '@/events/ProductAdded';

const webSocketEventListenerList  = [
    productAddedEvent
]

export default {
     listenToAllEvents:  ()=>{
          webSocketEventListenerList.forEach((listener)=>{
              console.log(listener);
              listener.listen();
          })
     },
    leaveAllEvents: () =>  {
         webSocketEventListenerList.forEach((listener)=>{
              listener.leave();
         })
    }
}
