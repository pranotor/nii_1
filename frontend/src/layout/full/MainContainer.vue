<template>
	<div class="main-wrapper" :class="[mainWrapperClass]">
		<!---Navigation-->
		<Navbar :topbarColor="topbarColor" :logo="require('@/assets/images/logo/logo-light-icon.png')" :title="logotitle" />
		<!---Sidebar-->
		<SideBar parent=".main-wrapper" :sidebarLinks="sidebarLinks"/>
		<!---Page Container-->
		<div class="main-container-fluid">
		<transition name="router-anim" enter-active-class="fade-enter-active fade-enter" leave-active-class="fade-leave-active fade-enter">
			
		<router-view></router-view>
		</transition>
		<!---Customizer / Settings-->
		
		</div>	
	</div>
</template>

<script>
 
import Navbar from '@/layout/full/header/Navbar.vue';
import SideBar from '@/layout/full/sidebar/SideBar.vue';
//import sidebarLinks from '@/layout/full/sidebar/sidebarlinks.js';
import themeConfig from '@/../themeConfig.js';

export default {
name: "MainContainer",
components: {
Navbar,
SideBar
},
data:() => ({
	topbarColor: themeConfig.topbarColor,
	logotitle: themeConfig.logotitle,
	sidebarLinks: [],
}),
methods: {
  updateTopbarColor(val) {
     this.topbarColor = val;
  },
  getMenu() {
        this.$http.get('/api/menu').then(response => {
           let links = response.data
           let link_nonchild = links.filter(item => item.child.length == 0).map(item => item.id)
           link_nonchild.forEach(item => {
               let sublink = links.filter(link => link.id == item).flat()
               //console.log(sublink)
               delete sublink[0].child
               //console.log(sublink)
           })
           this.sidebarLinks = links
        }).catch(error=>{
            console.log(error)
        });
    },
},
computed: {
    sidebarWidth: function() {
        return this.$store.state.sidebarWidth;
    },
    mainWrapperClass: function() {
        if(this.sidebarWidth == "default") {
            return "main-wrapper-default"
        } 
        else if(this.sidebarWidth == "mini") {return "main-wrapper-mini"}
        else if(this.sidebarWidth){ return "main-wrapper-full"}
        return "default"	
    },
},
mounted: function () {
    //this.sidebarLinks = sidebarLinksJs;
    this.getMenu();
},

}  	
</script>