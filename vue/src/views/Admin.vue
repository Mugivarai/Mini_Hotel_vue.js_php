<template>
  <div v-if="pageState==='loading' || pageState==='result'" class="loading-window">
    <div class="loading-box">
        <h1>{{ pageTextResult }}</h1>
    </div>
  </div>
  <div v-if="pageState==='login'" class="wrapper">
    <form action="" class="login">
        <h1>LOGIN</h1>
        <div class="login-item">
            <p>Введите номер</p>
            <input type="text" placeholder="Phone" v-model="phone" ref="refPhone" @change="phoneValidate">
            <span>{{ errorValidate.phone }}</span>
        </div>
        <div class="login-item">
            <p>Введите пароль</p>
            <input type="password" placeholder="Password" v-model="password" @change="passwordValidate" ref="refPassword">
            <span>{{ errorValidate.password }}</span>
        </div>
        <div class="login-item-button">
            <button type="button"  @click="confirmUser()">ВХОД</button>
            <button type="button" @click="resetForm()">ОЧИСТИТЬ</button>
        </div>
    </form>
  </div>
  <div v-if="pageState==='okey'" class="main-window">
    <main>
        <div class="log-out" @click="logOut()">
            <img src="@/image/log_out.png" alt="" title="Выйти из аккаунта">
        </div>
        <h1>СТРАНИЦА АДМИНИСТРАТОРА БД МИНИ-ОТЕЛЯ</h1>
        <div class="header-menu">
            <select name="tableName" v-model="tableNameSelect">
                <option v-for="(value,key) in nameTables" :value="value['tablename']" v-bind:key="key">
                    {{value['tablename']}}
                </option>
            </select>
            <button @click="fetchTable()">ОБНОВИТЬ</button>
        </div>
        <div class="search-input">
            <input type="search" v-model="search" @input="searchData()">
        </div>
        <div class="current-table" v-if="currentTable">
            <table v-show="sourceTableShow">
                <thead>
                    <tr @click="sortHead($event)">
                        <td v-for="(value,key) in currentTable['columns']" v-bind:key="key" :id="value">
                            {{ value }}
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(value,key) in currentTable['data']" v-bind:key="key">
                        <td v-for="(v,k) in value" v-bind:key="k">
                            {{v}}
                        </td>
                    </tr>
                </tbody>
            </table>
            <table v-show="searchTableShow">
                <thead>
                    <tr @click="sortHead($event)">
                        <td v-for="(value,key) in currentTable['columns']" v-bind:key="key" :id="value">
                            {{ value }}
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(value,key) in searchTable" v-bind:key="key" >
                        <td v-for="(v,k) in value" v-bind:key="k">
                            {{v}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </main>
  </div>
  <div v-show="resultModalShow" class="result-modal">
        <p>{{ resultModal }}</p>
        <button type="button" @click="resultModalShow=false">ЗАКРЫТЬ</button>
    </div>
</template>

<script>

export default {
    data(){
        return{
            resultModal: '',
            resultModalShow: false,
            sourceTableShow: true,
            searchTableShow: false,
            phone: '',
            password: '',
            errorValidate: {
                phone: '',
                password: ''
            },
            search: '',
            tableNameSelect: '',
            pageState: 'loading',
            pageTextResult: 'Загрузка...',
            nameTables: [],
            currentTable: [],
            sortList: {},
            searchTable: []
        }
    },
    created(){
        this.checkSessionAdmin();
    },
    methods:{
        async checkSessionAdmin(){
            try{
                const response = await fetch('http://localhost:8081/check_session_admin.php',{
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                    }
                });
                if(response.ok){
                    this.pageState = 'okey';
                    this.fetchNameTables();
                }else{
                    if(response.status == 400){
                        this.pageState = 'result';
                        this.pageTextResult = 'Выйдите из аккаунта пользователя';
                    }else if(response.status === 403){
                        this.pageState = 'login';
                    }
                }
            }
            catch(error){
                console.log('Error ',error);
            }
        },
        async confirmUser(){
            try{
                this.passwordValidate();
                this.phoneValidate();

                for(let v in this.errorValidate){
                    if(this.errorValidate[v] != ''){
                        return;
                    }
                }

                const response = await fetch('http://localhost:8081/log_in_admin.php',{
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        phone: this.phone,
                        password: this.password
                    })
                });
                console.log(response.status);
                if(response.ok){
                    this.pageState = 'okey';
                    window.location.reload();
                }else{
                    if(response.status == 505){
                        this.pageTextResult = 'Администратор уже в сети';
                        this.pageState = 'result';
                    }else if(500){
                        this.resultModal = 'Неверный пароль или телефон';
                        this.resultModalShow = true;
                    }
                }
            }
            catch(error){
                console.log('Error ',error);
            }
        },
        async fetchNameTables(){
            try{
                const response = await fetch('http://localhost:8081/admin.php/name_tables',{
                    method: 'GET',
                    credentials: 'include',
                    headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                    }
                });
                if(response.ok){
                    this.nameTables = await response.json();
                    this.tableNameSelect = this.nameTables[0]['tablename'];
                    this.fetchTable();
                }else{
                    console.log(response.status);
                }
            }
            catch(error){
                console.log('Error ',error);
            }
        },  
        async fetchTable(){
            try{
                this.sortList = {};
                const response = await fetch('http://localhost:8081/admin.php/get_data',{
                    method: 'POST',
                    credentials: 'include',
                    headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        nametable: this.tableNameSelect
                    })
                });
                if(response.ok){
                    this.currentTable = await response.json();
                    console.log(this.currentTable);
                }else{
                    console.log(response.status);
                }
            }
            catch(error){
                console.log('Error ',error);
            }
        },
        async logOut(){
            try{
                const response = await fetch('http://localhost:8081/admin.php/delete_session',{
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
                console.log('error:',error);
            }
        },
        resetForm(){
            this.phone='';
            this.password='';

            for(let v in this.errorValidate){
                this.errorValidate[v] = '';
            }

            this.$refs.refPhone.style.boxShadow = '';
            this.$refs.refPassword.style.boxShadow = '';
        },
        passwordValidate(){
            if(this.password=='' || this.password.length < 3){
                if (this.password == "") {
                    this.errorValidate.password = "Незаполненное поле";
                } else {
                    this.errorValidate.password = "Пароль должен быть больше 3 симовлов";
                }
                this.$refs.refPassword.focus();
                this.$refs.refPassword.style.boxShadow = "0px 0px 10px 0px rgb(255,0,0)";
                this.$refs.refPassword.addEventListener(
                    "input",
                    () => {
                        this.errorValidate.password = '';
                        this.$refs.refPassword.style.boxShadow = "";
                    },
                    { once: true }
                );
            }else{
                this.errorValidate.password = '';
                this.$refs.refPassword.style.boxShadow = "0px 0px 10px 0px rgb(0,255,0)";
            }
        },
        phoneValidate(){
            const testPhone = /^\+(7|8)\d{10}$/;
            if (this.phone == "" || !testPhone.test(this.phone)) {
                if (this.phone == "") {
                    this.errorValidate.phone = "Незаполненное поле";
                } else {
                    this.errorValidate.phone = 'Неккоректный ввод';
                }
                this.$refs.refPhone.focus();
                this.$refs.refPhone.style.boxShadow = "0px 0px 10px 0px rgb(255,0,0)";
                this.$refs.refPhone.addEventListener(
                    "input",
                    () => {
                        this.errorValidate.phone = '';
                        this.$refs.refPhone.style.boxShadow = "";
                    },
                    { once: true }
                );
            } else {
                this.errorValidate.phone = '';
                this.$refs.refPhone.style.boxShadow = "0px 0px 10px 0px rgb(0,255,0)";
            }
        },
        sortHead(event){
            if(event.target.tagName == 'TD'){
                var array;
                if(this.searchTableShow){
                    array = this.searchTable;
                }else{
                    array = this.currentTable['data'];
                }

                let id = event.target.id;

                if(id in this.sortList){
                    this.sortList[id]+=1;
                    if(this.sortList[id]>1){
                        this.sortList[id] = 0;
                    }
                }else{
                    this.sortList[id] = 0;
                }
                array = array.sort((a, b) => {
                    var valueA;
                    var valueB;
                    if(this.sortList[id]==0){
                        valueA = a[id];
                        valueB = b[id];
                    }else{
                        valueA = b[id];
                        valueB = a[id];
                    }

                    const isNumberA = !isNaN(parseFloat(valueA)) && isFinite(valueA);
                    const isNumberB = !isNaN(parseFloat(valueB)) && isFinite(valueB);

                    if (isNumberA && isNumberB) {
                        return valueA - valueB;
                    } else {
                        return String(valueA).localeCompare(String(valueB));
                    }
                });
                if(this.searchTableShow){
                    this.searchTable = array;
                }else{
                    this.currentTable['data'] = array;
                }
            }
        },
        searchData(){
            if(this.search == ''){
                this.searchTableShow = false;
                this.sourceTableShow = true;
                return;
            }

            this.searchTable = [];
            this.sourceTableShow = false;

            for(let v of this.currentTable['data']){
                let temp = Object.values(v);
                for(let value of temp){
                    if (String(value).toLowerCase().includes(this.search.toLowerCase())) {
                        this.searchTable.push(v); 
                        break; 
                    }
                }
            }

            this.searchTableShow = true;
        }
    }
}
</script>

<style scoped>

.loading-window {
    background: linear-gradient(90deg, #ffbac9,#ffffff);
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: calc(100vh - 60px);
}

.loading-box{
    width: 350px;
    height: 200px;
    background-color: rgb(255, 255, 255);
    border-radius: 5px;
    box-shadow: 1px 1px 10px 0px black;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 10px;
}

.loading-box h1{
    text-shadow: 1px 1px 5px black;
    font-family: 'Courier New', Courier, monospace;
    color: rgb(0, 0, 0);
}

.wrapper{
    font-family: 'Courier New', Courier, monospace;
    color: white;
    width:100%;
    height: calc(100vh - 60px);
    background-image: url('@/image/form_log.jpg');
    background-size: 100% 100%;
    background-repeat: no-repeat;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login{
    padding: 10px;
    width: 300px;
    backdrop-filter: blur(10px);
    border-radius: 5px;
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
    box-shadow: 1px 1px 10px 0px white;
    gap: 20px;
}

.login-item{
    min-height: 75px;
    height: auto;
    width: 100%;
}

.login-item p{
    margin:0%;
}

.login input{
    padding: 10px;
    width: 100%;
    box-sizing: border-box;
    border: 1px solid brown
}

.login input::placeholder{
    font-family: 'Courier New', Courier, monospace;
}

.login-item-button{
    width: 100%;
    display: flex;
    justify-content: space-evenly;
}

.login-item-button button{
    padding: 5px 10px;
    width: 35%;
    font-family: 'Courier New', Courier, monospace;
    font-weight: bold;
    cursor: pointer;
    border-radius: 0%;
    border: 1px solid brown;
    transition: all 0.5s;
}

.login-item span{
    font-size:12px;
    color: red;
}

.login-item-button button:hover {
    color: rgb(255, 255, 255);
    background-color: rgb(0, 0, 0);
    border: 1px solid white;
}

.result-modal{
    padding: 5px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    position: fixed;
    transform: translate(-50%,-50%);
    top:50%;
    z-index:10;
    left:50%;
    width:30%;
    background-color: rgb(255, 252, 252);
    color: rgb(0, 0, 0);
    border-radius: 5px;
}

.result-modal button{
    padding: 5px 10px;
    border-radius: 0%;
    border: 1px solid white;
    background-color: rgb(0, 0, 0);
    font-weight: bold;
    color: white;
    transition: all 0.5s;
    cursor: pointer;
}

.result-modal button:hover{
    color: black;
    background-color: white;
    border: 1px solid black;
}

.main-window{
    font-family: 'Courier New', Courier, monospace;
    width: 100%;
    height: auto;
}

main{
    width:100%;
    box-sizing: border-box;
    min-height: 1000px;
    background: linear-gradient(90deg, #ffbac9,#fd9090);
    background-attachment: fixed;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap:10px;
}

main h1{
    align-items: center;
    text-shadow: 1px 1px 5px black;
}

.header-menu{
    display: flex;
    width:90%;
    height: auto;
    gap: 5px;
}

.header-menu select{
    padding: 5px 10px;
    border-radius: 5px;
    width: 20%;
    box-shadow: 1px 1px 10px 0px black;
    cursor: pointer;
}

.header-menu button {
    border-radius: 5px;
    text-decoration: none;
    color: #000000;
    width:20%;
    padding: 10px 0px;
    cursor: pointer;
    background-color: rgb(255, 126, 126);
    border: 1px solid white;
    box-shadow: 1px 1px 10px 0px black;
    transition: all 0.5s;
    font-weight: bold;
}

.header-menu button:hover{
    color: black;
    background-color: #FFF2DF;
    border: 1px solid black;
}

.current-table{
    width:90%;
    height: auto;
    display: flex;
    justify-content: center;
}

.current-table table{
    border-collapse:collapse;
    background-color: rgb(255, 255, 255);
    box-shadow: 1px 1px 10px 0px black;
}

.current-table table thead{
    text-align: center;
    background-color: rgb(255, 145, 205);
}

.current-table table thead td{
    cursor:pointer;
}

.current-table table td{
    padding: 5px;
    border: 1px solid black;
}

.search-input{
    width: 90%;
    height: auto;
}

.search-input input{
    padding: 10px;
    border-radius: 10px;
    width: 100%;
    border: 3px solid red;
    box-shadow: 1px 1px 10px 0px black;
}

.log-out{
    cursor: pointer;
    position: absolute;
    top: 70px;
    right: 5px;
    width:35px;
    height: 35px;
}

.log-out img{
    width: 100%;
    height: 100%;
}

</style>