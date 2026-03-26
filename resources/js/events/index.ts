import productAddedEvent from '@/events/ProductAdded';
import productDeletedEvent from '@/events/ProductDeleted';

const webSocketEventListenerList  = [
    productAddedEvent,
    productDeletedEvent
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
