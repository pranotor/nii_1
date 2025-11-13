<template>
  <vs-row>
    <vs-col vs-lg="6" vs-xs="12">
      <h3 class="mt-2 mb-3">{{ $route.name }}</h3>
    </vs-col>
    <vs-col vs-lg="6" vs-xs="12" class="d-none d-md-block">
      <vs-breadcrumb v-if="$route.meta.breadcrumb" align="right">
        <li>
          <router-link to="/starterkit">
            <vs-icon icon-pack="mdi mdi-home"></vs-icon>
          </router-link>
          <span class="vs-breadcrum--separator">/</span>
        </li>
        <li v-for="(bcrumb, index) in $route.meta.breadcrumb.slice(0,-1)" :key="index">
          <router-link :to="bcrumb.url" v-if="bcrumb.url">{{ bcrumb.title }}</router-link>
          <span class="text-primary cursor-default" v-else>{{ bcrumb.title }}</span>
          <span class="vs-breadcrum--separator">/</span>
        </li>
        <li class="inline-flex">
          <span
            v-if="$route.meta.breadcrumb.slice(-1)[0].active"
            class="cursor-default"
          >{{ $route.meta.breadcrumb.slice(-1)[0].title }}</span>
        </li>
      </vs-breadcrumb>
    </vs-col>
  </vs-row>
</template>

<script>
export default {
  name: "Breadcrumb"
};
</script>