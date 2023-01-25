<script lang="ts" setup>
import { RouteLocation } from 'vue-router'
// @ts-ignore
// eslint-disable-next-line import/named
import { useLocalePath } from '#imports'
import type { RouteType } from '~~/.nuxt/typed-link'

const props = defineProps<{
  to: RouteType
}>()

const localePath = useLocalePath()
const route = props.to as unknown as RouteLocation
</script>

<template>
  <nuxt-link
    :to="to ? localePath(route) : '/'"
  >
    <slot />
  </nuxt-link>
</template>
