<template>
  <navBar class="navbar"></navBar>
  <div class="main-flex">
    <router-view></router-view>
  </div>
</template>

<script>
import navBar from '@/components/navBar.vue'
export default{
  components: {
    navBar
  },
  computed: {
    currentRoute() {
      return this.$route.path; 
    }
  }
}
</script>

<style>

html {
  scroll-behavior: smooth;
  scroll-padding: -60px;
}

.app{
  height: auto;
}

body {
  margin: 0%;
  padding: 0%;
}

.main-flex{
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: auto;
}

</style>
