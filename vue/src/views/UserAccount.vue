<template>
  <div class="account-back">
    <div v-if="session === ''" class="load-window">
        <h1>Загрузка...</h1>
    </div>
    <div v-if="session === 'true'" class="main-window">
        <nav class="navigation">
            <div class="nav-item" @click="deleteSession()">
                <img src="@/image/log_out.png" alt="" title="Выход из аккаунта">
            </div>
            <div class="nav-item">
                <a href="/user_account#section-history">
                    <img src="@/image/history-icon.png" alt="" title="История заказов">
                </a>
            </div>
            <div class="nav-item">
                <a href="/user_account#section-order">
                    <img src="@/image/order-icon.png" alt="" title="Сделать заказ">
                </a>
            </div>
        </nav>
        <main class="main-column-user">
            <div class="main-user-container-info">
                <div class="main-user-info">
                    <img src="@/image/user-account-profile.png" alt="">
                    <h1>{{mainUserInfo[0]['fio']}}</h1>
                    <p>Email: {{ mainUserInfo[0]['email'] }}</p>
                    <p>Phone: {{ mainUserInfo[0]['telephone'] }}</p>
                </div>
            </div>
            <section class="create-order" id="section-order">
                <h1>ЗАКАЗАТЬ НОМЕР</h1>
                <div v-if="bookedCountRoom && bookedCountRoom.length > 0">
                    <div class="type-room-item" v-for="(value,i) in bookedCountRoom" v-bind:key="i" :id="'item-room'+i">
                        <h1>{{ value['name'] }}</h1>
                        <div class="type-room-type-flex">
                            <div class="room-type-img">
                                <img :src="value['photo']" alt="">
                            </div>
                            <div v-if="calendarDays && calendarDays.length > 0" class="calendar">
                                <p>{{calendarHeadText}}</p>
                                <table :id="'calendar-'+i">
                                    <thead>
                                        <tr>
                                            <td>Пн</td>
                                            <td>Вт</td>
                                            <td>Ср</td>
                                            <td>Чт</td>
                                            <td>Пт</td>
                                            <td>Сб</td>
                                            <td>Вс</td>
                                        </tr>
                                    </thead>
                                    <tbody @click="inputData($event,'#calendar-'+i)">
                                        <tr v-for="(value,key) in calendarDays" v-bind:key="key">
                                            <td v-for="(v,k) in calendarDays[key]" v-bind:key="k" :class="v['class']">
                                                {{ v['day'] }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="calendar-buttons">
                                    <button @click="resetStyleTd('#item-room'+i,i)">ОЧИСТИТЬ</button>
                                    <button @click="getFreeRooms('#item-room'+i,i)">ПРОВЕРИТЬ</button>
                                </div>
                            </div>
                        </div>
                        <div class="not-free-rooms display-none">Нет свободных номеров</div>
                        <div class="free-booked-room-container" v-if="freeBookRoom[i] && freeBookRoom[i].length>0" :id="'room-booked-container-'+i">
                            <h1>Свободные номера</h1>
                            <div class="free-booked-room" @click="chooseRoomNumber($event,'#room-booked-container-'+i)">
                                <div v-for="(v,k) in freeBookRoom[i]" v-bind:key="k" class="free-booked-item">
                                    {{v['idintifier']}}
                                </div>
                            </div>
                            <h1>Дополнительные услуги</h1>
                            <div class="add-services" v-if="addServices && addServices.length>0" @click="chekedAddServices($event)">
                                <div v-for="(v,k) in addServices" v-bind:key="k">
                                    <h1>{{ v['name'] }}</h1>
                                    <div class="add-services-description">
                                        <p>{{ v['description'] }}</p>
                                        <div class="add-serv-checkbox add-serv-notchecked"></div>
                                    </div>
                                </div>
                            </div>
                            <button class="payment-button" @click="computedSumm('#item-room'+i,i)">РАССЧИТАТЬ</button>
                        </div>
                    </div>
                </div>
            </section>
            <section class="history-container" id="section-history">
                <h1>ИСТОРИЯ</h1>
                <div class="history-header-buttons">
                    <button @click="bookedShow=true">ПОКАЗАТЬ</button>
                    <button @click="bookedShow=false">СКРЫТЬ</button>
                </div>
                <div class="history-body-buttons" v-show="bookedShow">
                    <button @click="fetchBookedLogInfo()">ОБНОВИТЬ</button>
                    <button @click="sortBookedLog(1)">НЕ ОПЛАЧЕННЫЕ</button>
                    <button @click="sortBookedLog(2)">ОПЛАЧЕННЫЕ</button>
                    <button @click="sortBookedLog(3)">САМЫЕ ДОРОГИЕ</button>
                    <button @click="sortBookedLog(4)">САМЫЕ ДЕШЕВЫЕ</button>
                </div>
                <div v-if="bookedLogInfo && bookedLogInfo.length > 0" v-show="bookedShow">
                    <div class="history-booked-item" v-for="(value,i) in bookedLogInfo" v-bind:key="i">
                        <h1>{{ value['room_name'] }}</h1>
                        <img :src="value['photo']" alt="">
                        <p>Дата брони: {{value['date_booked']}}</p>
                        <p>Дата заезда: {{value['date_check_in']}}</p>
                        <p>Дата выезда: {{ value['date_departure'] !== null ? value['date_departure'] : '' }}</p>
                        <p>Стомиость: {{ value['summ'] !== null ? value['summ'] : '' }}</p>
                        <p>Номер комнаты: {{value['id_room']}}</p>
                        <p>ФИО сотрудника: {{ value['employee_fio'] }}</p>
                        <p>Статус: {{ value['summ'] === null ? 'Не оплачено' : 'Оплачено' }}</p>
                        <p>Дополнительные услуги: {{ value['add_services'].length == 0 ? 'Не были заказаны' : value['add_services'].join(', ') }}</p>
                        <div style="clear: both"></div>
                        <button @click="checkPaymentSumm(value['id'])" v-if="value['summ'] === null" class="payment-button" style="margin-left:25%">ОПЛАТИТЬ</button>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <div v-if="session === 'false'" class="error-window">
        <h1>Вы не авторизованы</h1>
        <div class="my-link">
            <a href="/login">LOG IN</a>
            <a href="/signup">SIGN UP</a>
        </div>
    </div>
  </div>
  <div class="popover-payment" v-show="paymentPopover">
    <div class="popover-container" v-if="popoverState==='' || popoverState==='payment'">
        <h1>К ОПЛАТЕ {{ summ }}.00 РУБ.</h1>
        <div class="popover-buttons">
            <button @click="createOrder()" v-if="popoverState===''">ЗАКАЗАТЬ</button>
            <button @click="createPayment()" v-if="popoverState==='payment'">ПОДТВЕРДИТЬ</button>
            <button @click="paymentPopover=false; pathForPayment='';">ОТМЕНА</button>
        </div>
    </div>
    <div class="popover-container" v-if="popoverState==='loading'">
        <h1>Загрузка...</h1>
    </div>
    <div class="popover-container" v-if="popoverState==='result'">
        <h1>{{ popoverResultText }}</h1>
        <div class="popover-buttons">
            <button @click="paymentPopover=false; pathForPayment=''; popoverState='';resetStyleTd(pathForPayment,indexForPayment);">ЗАКРЫТЬ</button>
        </div>
    </div>
  </div>
  
</template>

<script>

export default {
    data(){
        return{
            popoverResultText: '',
            popoverState: '',
            session: '',
            summ: 0,
            paymentPopover: false,
            notFreeRoom: false,
            mainUserInfo: [],
            bookedLogInfo: [],
            bookedCountRoom: [],
            calendarDays: [],
            freeBookRoom: [],
            addServices: [],
            selectedAddServId: [],
            calendarHeadText: '',
            pathForPayment: '',
            indexForPayment: 0,
            bookedShow: false,
            dateIn: '',
            dateOut: '',
            currentIdPaymentBooked: 0
        }
    },
    created(){
        this.checkSession();
        this.pushCalendar();
    },
    methods:{
        async checkSession(){
            try {
                let response = await fetch('http://localhost:8081/check_session_user.php', {
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                    }
                });
                console.log(response);

                if (response.status == 200) {
                    this.session = 'true';
                    this.fetchMainUserInfo();
                    this.fetchBookedLogInfo();
                    this.fetchBookedInfo();
                    this.fetchAddServices();
                } else {
                    this.session = 'false';
                }
                
                console.log('session bool',this.session);
            } catch (error) {
                console.error('Ошибка при отправке запроса:', error);
                this.session = 'false';
            }
        },
        async fetchMainUserInfo(){
            try{
                const response = await fetch('http://localhost:8081/user_account_info.php/main_user_info',{
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                    }
                });

                if(response.status == 200){
                    this.mainUserInfo = await response.json();
                    console.log('Array mainUserInfo: ',this.mainUserInfo);
                }else{
                    console.log('Error: ',response.status);
                }
            }
            catch(error){
                console.log('Error: ',error);
            }
        },
        async fetchBookedLogInfo(){
            try{
                const response = await fetch('http://localhost:8081/user_account_info.php/booked_log',{
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                    }
                });
                if(response.status == 200){
                    this.bookedLogInfo = await response.json();
                    console.log('Array mainUserInfo: ',this.bookedLogInfo);
                }else{
                    console.log('Error: ',response.status);
                }
            }
            catch(error){
                console.log('Error: ',error);
            }
        },
        async deleteSession(){
            try{
                const response = await fetch('http://localhost:8081/user_account_info.php/log_out',{
                    method: 'DELETE',
                    credentials: 'include',
                    headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                    }
                });

                if(response.ok){
                    window.location.reload();
                }else{
                    console.log(response.status);
                }
            }
            catch(error){
                console.log('Error: ',error);
            }
        },
        async fetchBookedInfo(){
            try{
                const response = await fetch('http://localhost:8081/booked_info.php/count_type_of_room');
                if(response.ok){
                    this.bookedCountRoom = await response.json();
                    for(let i=0;i<this.bookedCountRoom;i++){
                        this.freeBookRoom.push([]);
                    }
                }else{
                    console.log(response.status);
                }
            }
            catch(error){
                console.log('Error: ',error);
            }
        },
        async fetchFreeBookRoom(date_in_p,date_out_p,i,path){
            try{
                console.log(path+' .not-free-rooms');
                let temp = document.querySelector(path+' .not-free-rooms');
                if(temp.classList.contains('display-block')){
                    temp.classList.remove('display-block');
                    temp.classList.add('display-none');
                }
                const response = await fetch('http://localhost:8081/booked_info.php/free_rooms',{
                    method: 'POST',
                    headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        date_in: date_in_p,
                        date_out: date_out_p,
                        id: this.bookedCountRoom[i]['id']
                    })
                });
                if(response.ok){
                    let temp = await response.json();
                    this.freeBookRoom[i] = [];
                    this.freeBookRoom[i] = this.freeBookRoom[i].concat(temp);
                    if(temp.length == 0){
                        this.notFreeRoom = true;
                    }
                }else{
                    if(temp.classList.contains('display-none')){
                        temp.classList.remove('display-none');
                    }
                    temp.classList.add('display-block');
                }
            }
            catch(error){
                console.log('Error: ',error);
            }
        },
        async fetchAddServices(){
            try{
                const response = await fetch('http://localhost:8081/additional_services');
                if(response.ok){
                    this.addServices = await response.json();
                }else{
                    console.log(response.status);
                }
            }
            catch(error){
                console.log('Ошибка:', error);
            }
        },
        async createOrder(){
            try{
                this.popoverState = 'loading';
                let addServ = Array.from(this.selectedAddServId).map(Number);
                let room = Number.parseInt(document.querySelector(this.pathForPayment+' .room-green').textContent);
                const response = await fetch('http://localhost:8081/create_order.php/create',{
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        date_in: this.dateIn,
                        date_out: this.dateOut,
                        date_booked: new Date().toISOString(),
                        id_room: room,
                        add_serv: addServ
                    })
                });
                if(response.ok){
                    this.popoverResultText = 'Заказ оформлен!';
                    this.popoverState = 'result';
                }else{
                    if(response.status == 400){
                        this.popoverResultText = 'У вас есть больше 1 незакрытого заказа';
                    }else{
                        this.popoverResultText = 'Произошла ошибка';
                    }
                    
                    this.popoverState = 'result';
                }
            }
            catch(error){
                console.log('Ошибка:', error);
            }
        },
        async checkPaymentSumm(id){
            try{
                this.paymentPopover = true;
                this.currentIdPaymentBooked = id;
                this.popoverState = 'loading';
                console.log(this.popoverState);
                const response = await fetch('http://localhost:8081/booked_info.php/check_summ',{
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_booked: id
                    })
                });
                console.log(response.status);
                if(response.ok){
                    let temp = await response.json();
                    this.summ = temp[0]['summ'];
                    this.popoverState = 'payment';
                }else{
                    this.popoverResultText = 'Произошла ошибка';
                    this.popoverState = 'result';
                }
            }
            catch(error){
                console.log('Ошибка:', error);
            }
        },
        async createPayment(){
            try{
                this.popoverState = 'loading'
                const response = await fetch('http://localhost:8081/booked_info.php/create_payment',{
                    method: 'PUT',
                    credentials: 'include',
                    headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        id_booked: this.currentIdPaymentBooked,
                        date: new Date().toISOString(),
                        payment_type: 1
                    })
                });
                if(response.ok){
                    this.popoverResultText = 'ОПЛАТА ПРОШЛА УСПЕШНО'
                    this.popoverState = 'result';
                }else{
                    this.popoverResultText = 'ОПЛАТА НЕ ПРОШЛА'
                    this.popoverState = 'result';
                }
            }
            catch(error){
                console.log('Ошибка:', error);
            }
        },
        pushCalendar(){
            function toRussianDay(day){
                return (day+6)%7;
            }

            function formatDate(date) {
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                return date.toLocaleDateString(undefined, options);
            }

            let tempArray = [];
            let date = new Date();
            let countDaysMonth = 31;
            let currentDayWeek = toRussianDay(date.getDay());
            this.calendarHeadText = `Booking Period: ${formatDate(date)}`;

            //fitsts gray days
            date.setDate(date.getDate() - currentDayWeek);
            for(let i=0;i<currentDayWeek;i++){
                tempArray.push({'class':'day-gray', 'day':date.getDate()});
                date.setDate(date.getDate() + 1);
            }
            //none-color
            for(let i=0;i<countDaysMonth;i++){
                tempArray.push({'class':'calendar-day day-none-color', 'day':date.getDate(),'fullDate':date.toISOString()});
                date.setDate(date.getDate() + 1);
            }
            this.calendarHeadText += ` - ${formatDate(date)}`;
            //last gray day
            let temp = toRussianDay(date.getDay());
            for(let i=0;i<7-temp;i++){
                tempArray.push({'class':'day-gray', 'day':date.getDate()});
                date.setDate(date.getDate() + 1);
            }
            temp = [];
            for(let i=0;i<(7*Math.ceil(countDaysMonth/7));i++){
                temp.push(tempArray[i]);
                if((i>0) && (i%7 === 6)){
                    this.calendarDays.push(temp);
                    temp = [];
                }
            }
        },
        inputData(event,path) {
            if (event.target.tagName === 'TD') {
                if (event.target.classList.contains('day-none-color')) {
                    let td = document.querySelectorAll(path+' .day-green');
                    if(td.length == 0){
                        event.target.classList.remove('day-none-color');
                        event.target.classList.add('day-green');
                        event.target.classList.add('day-first');
                    }else if(td.length > 0){
                        event.target.classList.remove('day-none-color');
                        event.target.classList.add('day-green');
                        let td = document.querySelectorAll(path+' tbody td');
                        let left;
                        let right;
                        for(let i=0;i<td.length;i++){
                            if(td[i].classList.contains('day-green')){
                                left = i;
                                break;
                            }
                        }
                        for(let i=td.length-1;i>0;i--){
                            if(td[i].classList.contains('day-green')){
                                right = i;
                                break;
                            }
                        }
                        for(let i=left;i<=right;i++){
                            if(!td[i].classList.contains('day-green')){
                                td[i].classList.remove('day-none-color');
                                td[i].classList.add('day-green');
                            }
                        }
                    }
                    
                }else if(event.target.classList.contains('day-green')){
                    let td = document.querySelectorAll(path+' .day-green');
                    if(td.length==1){
                        return;
                    }
                    td = document.querySelectorAll(path+' tbody td');
                    event.target.classList.add('temp');
                    let left;
                    let right;
                    for(let i=0;i<td.length;i++){
                        if(td[i].classList.contains('temp')){
                            left = i;
                            td[i].classList.remove('temp');
                            break;
                        }
                    }
                    for(let i=td.length-1;i>0;i--){
                        if(td[i].classList.contains('day-first')){
                            right = i;
                            break;
                        }
                    }
                    if(left>right){
                        let temp = left;
                        left = right;
                        right = temp;
                    }
                    for(let i=0;i<td.length;i++){
                        if(i<left || i>right){
                            if(td[i].classList.contains('day-green')){
                                td[i].classList.remove('day-green');
                                td[i].classList.add('day-none-color');
                            }
                        }
                    }
                }
            }
        },
        resetStyleTd(path,i){
            let temp = document.querySelector(path+' .not-free-rooms');
            if(temp.classList.contains('display-block')){
                temp.classList.remove('display-block');
                temp.classList.add('display-none');
            }
            this.notFreeRoom = false;
            let td = document.querySelectorAll(path+' .day-green');
            if(td.length==0)return;
            for(let i=0;i<td.length;i++){
                td[i].classList.remove('day-green');
                td[i].classList.add('day-none-color');
            }
            document.querySelector(path+' .day-first').classList.remove('day-first');
            this.freeBookRoom[i] = [];
        },
        getFreeRooms(path,i){
            let temp = document.querySelectorAll(path+' tbody .day-green'); 
            if(temp.length == 0)return;
            let td = document.querySelectorAll(path+' tbody td');
            let left;
            let right;
            for(let i=0;i<td.length;i++){
                if(td[i].classList.contains('day-green')){
                    left = i;
                    break;
                }
            }
            for(let i=td.length-1;i>0;i--){
                if(td[i].classList.contains('day-green')){
                    right = i;
                    break;
                }
            }
            temp = this.calendarDays.flat();
            this.dateIn = temp[left]['fullDate'];
            this.dateOut = temp[right]['fullDate'];
            this.fetchFreeBookRoom(this.dateIn,this.dateOut,i,path);
        },
        chooseRoomNumber(event,path){
            if(event.target.classList.contains('free-booked-item')){
                if(event.target.classList.contains('room-green')){
                    event.target.classList.remove('room-green');
                    return;
                }
                let temp = document.querySelector(path+' .room-green');
                console.log(temp);
                if(temp){
                    temp.classList.remove('room-green');
                }
                event.target.classList.add('room-green');

            }
        },
        chekedAddServices(event){
            if(event.target.classList.contains('add-serv-checkbox')){
                if(event.target.classList.contains('add-serv-checked')){
                    event.target.classList.remove('add-serv-checked');
                    event.target.classList.add('add-serv-notchecked');
                }else{
                    event.target.classList.add('add-serv-checked');
                    event.target.classList.remove('add-serv-notchecked');
                }
            }
        },
        computedSumm(path,i){
            if(document.querySelectorAll(path+' .room-green').length == 0){
                alert('Выберите номер');
                return;
            }
            this.paymentPopover=true;
            let addServSumm = 0;
            let ch = document.querySelectorAll(path+' .add-serv-checkbox');
            for(let i=0;i<ch.length;i++){
                if(ch[i].classList.contains('add-serv-checked')){
                    this.selectedAddServId.push(this.addServices[i]['id']);
                    console.log(Number.parseInt(this.addServices[i]['cost']));
                    addServSumm += Number.parseInt(this.addServices[i]['cost']);
                }
            }
            let count = document.querySelectorAll(path+' .day-green').length;
            addServSumm += Number.parseInt(this.bookedCountRoom[i]['price'])*count;
            this.summ = addServSumm;
            this.pathForPayment = path;
            this.indexForPayment = i;
        },
        sortBookedLog(direction){
            switch(direction){
                case 1:{
                    this.bookedLogInfo = this.bookedLogInfo.sort((a,b) => (Number.parseInt(a.status) - Number.parseInt(b.status)));
                    break;
                }
                case 2:{
                    this.bookedLogInfo = this.bookedLogInfo.sort((a,b) => (Number.parseInt(b.status) - Number.parseInt(a.status)));
                    break;
                }
                case 3:{
                    this.bookedLogInfo = this.bookedLogInfo.sort((a,b) => (Number.parseFloat(b.summ) - Number.parseFloat(a.summ)));
                    break;
                }
                case 4:{
                    this.bookedLogInfo = this.bookedLogInfo.sort((a,b) => (Number.parseFloat(a.summ) - Number.parseFloat(b.summ)));
                    break;
                }
            }
        }
    }
}
</script>

<style scoped>

.account-back {
    font-family: 'Courier New', Courier, monospace;
    background-color: #FFE0B2;
    width: 100%;
    min-height: 1000px;
    color: #FFF2DF;
}

.error-window,
.load-window{
    position: fixed;
    top:50%;
    left: 50%;
    transform: translate(-50%,-50%);
    width: 350px;
    height: 200px;
    background-color: rgb(0, 0, 0);
    border-radius: 5px;
    box-shadow: 1px 1px 10px 0px black;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 10px;
    flex-direction: column;
}

.main-window {
    width: 100%;
    height: 100%;
    display: flex;
    padding-bottom: 40px;
}

.main-column-user {
    flex-grow: 1;
    display: flex;
    align-items: center;
    flex-direction: column;
    gap:40px;
}

.main-user-info{
    display: flex;
    flex-direction: column;
    justify-content: space-evenly;
    text-shadow: 1px 1px 5px white;
    border-radius: 5px;
    box-shadow: 1px 1px 10px 0px black;
    background-color: #000000;
    padding: 10px;
    width:90%;
    box-sizing: border-box;
}

.main-user-info img{
    margin: auto;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    box-shadow: 1px 1px 10px 0px white;
}

.main-user-info h1{
    text-align: center;
}

.main-user-info p{
    margin:0%;
    padding:0%;
    border-bottom: 1px solid white;
}

.main-user-container-info{
    padding: 5px 0px;
    display: flex;
    justify-content: center;
    align-items: center;
    width:100%;
    height: auto;
}

.navigation {
    margin-left: 5px;
    box-shadow: 1px 1px 10px 0px black;
    position: fixed;
    top: 50%;
    left: 0%;
    transform: translateY(-50%);
    border-radius: 20px;
    height: 50vh;
    width: 50px;
    background-color: black;
    display: flex;
    align-items: center;
    justify-content: space-evenly;
    gap: 10px;
    flex-direction: column;
    padding-top: 10px;
    padding-bottom: 10px;
}

.nav-item{
    display:flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: auto;
    cursor: pointer;
}

.nav-item img{
    width: 35px;
    height: 35px;
}

section > h1{
  font-family: 'Courier New', Courier, monospace;
  text-shadow: 1px 1px 5px white;
  font-size: 4em;
  margin: 0%;
  text-align: center;
}

.create-order,
.history-container{
    width:90%;
    height: auto;
}

.create-order h1,
.history-container h1{
    box-sizing: border-box;
    background-color: black;
    box-shadow: 1px 1px 10px 0px black;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
}

.history-body-buttons,
.popover-buttons,
.calendar-buttons,
.my-link,
.history-header-buttons{
    margin-top:5px;
    display: flex;
    width: 100%;
    justify-content: center;
    align-items: center;
    height: auto;
}

.history-body-buttons button{
    text-decoration: none;
    color: #FFF2DF;
    width:20%;
    padding: 10px 0px;
    cursor: pointer;
    background-color: black;
    border: 1px solid white;
    box-shadow: 1px 1px 10px 0px black;
    transition: all 0.5s;
    font-weight: bold;
}

.popover-buttons button,
.payment-button,
.calendar-buttons button,
.my-link a,
.history-header-buttons button{
    text-decoration: none;
    color: #FFF2DF;
    width:50%;
    padding: 10px 0px;
    cursor: pointer;
    background-color: black;
    border: 1px solid white;
    box-shadow: 1px 1px 10px 0px black;
    transition: all 0.5s;
    font-weight: bold;
}

.history-body-buttons button:hover,
.popover-buttons button:hover,
.payment-button:hover,
.calendar-buttons button:hover,
.my-link a:hover, 
.history-header-buttons button:hover{
    color: black;
    background-color: #FFF2DF;
    border: 1px solid black;
}

.type-room-item,
.history-booked-item{
    text-shadow: 1px 1px 5px white;
    box-sizing: border-box;
    width:100%;
    border-radius: 5px;
    height: auto;
    margin-top: 5px;
    background-color: black;
    box-shadow: 1px 1px 10px 0px black;
    padding: 10px;
}

.history-booked-item img{
    width:400px;
    height: 250px;
    float:left;
    margin-right: 40px;
    border-radius: 5px;
}

.history-booked-item h1{
    text-align: center;
    font-size: 1.5em;
    margin-top: 0%;
}

.type-room-item{
    display: flex;
    align-items: center;
    flex-direction: column;
}

.room-type-img{
    width: 50%;
    height: auto;
}

.room-type-img img{
    border-radius: 5px;
    width: 100%;
    height: 300px;
}

.type-room-type-flex {
    width: 100%;
    height: auto;
    display: flex;
    justify-content: space-evenly;
    align-items: flex-start;
}

.calendar {
    height: 300px;
    border-radius: 5px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
}

.calendar p{
    margin:0%;
    padding:0%;
}

.calendar table{
    color:black;
    background-color: white;
    text-align: center;
    border-collapse:collapse;
    border-radius: 5px;
    overflow: hidden;
    box-shadow: 1px 1px 10px 0px white;
    font-weight: bold;
}

.calendar table td{
    padding: 5px 10px;
}

.day-gray{
    background-color: rgba(214, 122, 122, 0.623);
}

.day-green{
    background-color: lightgreen;
    border-radius: 5px;
}

.calendar thead{
    background-color: rgb(0, 0, 0);
    color: white;
}

.calendar-day{
    cursor:pointer;
}

.free-booked-room-container{
    width: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 2px;
}

.free-booked-room {
    display: flex;
    align-items: flex-start;
    gap:5px;
}

.free-booked-item{
    color:#000000;
    padding: 5px 10px;
    border-radius: 5px;
    background-color: white;
    text-shadow: 1px 1px 5px black;
    box-sizing: border-box;
    width: 40px;
    text-align: center;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.5s;
}

.free-booked-item:hover{
    background-color: green;
}

.room-green{
    background-color: green;
}

.add-services-description{
    display:flex;
    align-items: center;
    justify-content: space-between;
    gap: 5px;
}

.add-serv-checked{
    border: 2px solid black;
    background-color: white;
    box-shadow: 1px 1px 10px 0px lightgreen;
}

.add-serv-notchecked{
    border: 2px solid white;
    background-color: black;
    box-shadow: 1px 1px 10px 0px black;
}

.add-serv-checkbox{
    cursor: pointer;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    transition: all 0.5s;
}

.popover-payment{
    width:100vw;
    height: 100vh;
    background-color: #000000b0;
    position: fixed;
    z-index: 20;
    top: 0%;
    left: 0%;
    display:flex;
    align-items:center;
    justify-content: center;
}

.popover-container{
    text-align: center;
    font-family: 'Courier New', Courier, monospace;
    width: 40%;
    height: 200px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-evenly;
    background-color: white;
    border-radius: 5px;
}

.display-none{
    display: none;
}

.display-block{
    display: block;
}

</style>