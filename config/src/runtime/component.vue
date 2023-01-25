<script lang="ts" setup>
import { RouteLocation } from 'vue-router'
import type { RouteType } from '~~/.nuxt/typed-link'

const props = defineProps<{
  to: RouteType
}>()

const route = props.to as unknown as RouteLocation
</script>

<template>
  <nuxt-link
    :to="to ? route : '/'"
  >
    <slot />
  </nuxt-link>
</template>
