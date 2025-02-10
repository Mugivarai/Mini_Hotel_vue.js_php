<template>
  <header>
        <div>
            <a :class="(currentRoute == '/') ? 'point-main':''" href="/">Mini-Hotel</a>
        </div>
        <nav>
            <a :class="(currentRoute == '/login') ? 'point link-a':'link-a'" href="/login">Log in</a>
            <a :class="(currentRoute == '/signup') ? 'point link-a':'link-a'" href="/signup">Sign up</a>
            <a :class="(currentRoute == '/user_account') ? 'point link-account':'link-account'" href="/user_account">
                <img src="@/image/user-account.png" alt="">
            </a>
        </nav>
    </header>
</template>

<script>
export default {
    data(){
        return{
            lastScrollTop:  0,
            headerePath: null
        };
    },
    methods:{
        hiddenHeader(){
            let currentScroll = window.pageYOffset;

            if (currentScroll > this.lastScrollTop) {
                this.headerePath.style.top = '-70px'; 
            } else {
                this.headerePath.style.top = '0'; 
            }
            
            this.lastScrollTop = currentScroll; 
        }
    },
    mounted(){
        this.headerePath = document.getElementsByTagName('header')[0] 
        window.addEventListener('scroll',this.hiddenHeader);
        
    },
    computed: {
        currentRoute() {
            return this.$route.path; 
        }
    }
}
</script>

<style scoped>

header {
    top:0;
    z-index: 10;
    position: sticky;
    width: 100%;
    height: 60px;
    background-color: rgb(0, 0, 0);
    box-shadow: 0px -5px 10px 0px rgb(255, 255, 255);
    display: flex;
    justify-content: center;
    transition: top 0.5s ease;
    box-sizing: border-box;
}

header > nav {
    display: flex;
    width: 60%;
    max-width: 814.5px;
    height: auto;
    justify-content: end;
    align-items: center;
}

.link-a {
    text-decoration: none;
    color: white;
    font-size: 1.5vw;
    padding-left: 5px;
    padding-right: 5px;
    height: 100%;
    display: flex;             
    align-items: center;       
    justify-content: center;
    text-shadow: 1px 1px 5px white;
    transition: all 0.5s;
    width: 20.0%;
}

.link-account {
    width: 20.0%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;

}

.link-account img{
    width: 40px;
    height: 40px;
    filter: drop-shadow(0px 0px 5px rgba(255,255,255,0.9));
}


.link-a:hover{
    background-color: rgba(255, 255, 255, 0.781);
    color: black;
}

header > div{
    display: flex;
    width: 40%;
    height: auto;
    align-items: center;
    justify-content: center;
    max-width: 543px;
}

header > div > a{
    width: 35%;
    text-decoration: none;
    color: white;
    font-size: 2em;
    text-shadow: 1px 1px 5px white;
    text-align: center;
}

.point{
    background: linear-gradient(90deg, #000000,#80626e);
    background-size: 300% 300%;
    animation: gradientShift 10s ease infinite;
}

@keyframes gradientShift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.point-main{
    text-shadow: 2px 2px 5px rgb(247, 5, 5);
    color: rgb(204, 21, 91);
}

@media (min-width: 1358px){
    header > nav > a{
        font-size: 1.3em;
    }
}

@media (max-width:950px){
    header > div {
        font-size: 10px;
    }
}

@media (max-width: 600px){
    header > div {
        font-size: 8px;
    }
}

</style>