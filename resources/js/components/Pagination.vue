<template>
  <div v-if="hasLinksMoreThan3" :class="class" class="">
    <div class="flex flex-wrap shrink-0">
      <template v-for="(link, key) in links" :key="key">
        <button
          v-if="link.url === null"
          disabled
          class="mr-1 mb-1 px-3 py-2 border rounded-brand flex items-center justify-center opacity-70 min-w-10"
          v-html="link.label"
        />
        <Link
          v-else
          class="mr-1 mb-1 px-3 py-2 border rounded-brand flex items-center justify-center min-w-10"
          :class="{ 'bg-black text-white': link.active }"
          :href="link.url"
          v-html="link.label"
          preserve-scroll
        />
      </template>
    </div>
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps({
  class: String,
  links: {
    type: Array,
    default: () => [], // предотвращает Missing required prop и падение на length
  },
})

const hasLinks = computed(() => Array.isArray(props.links) && props.links.length > 0)
const hasLinksMoreThan3 = computed(() => Array.isArray(props.links) && props.links.length > 3)

/**
 * В links от Laravel paginator обычно:
 * [0] = Prev, [...numbers], [last] = Next
 * Метки Prev/Next могут приходить как &laquo; и &raquo; — нормализуем.
 */
const prevLink = computed(() => (props.links.length ? props.links[0] : null))
const nextLink = computed(() => (props.links.length ? props.links[props.links.length - 1] : null))
const currentPage = computed(() => {
  const active = props.links.find?.(l => l.active)
  return active ? String(active.label).replace(/&nbsp;/g, ' ') : ''
})
const normalizeLabel = (label) => {
  if (!label) return ''
  if (typeof label === 'string') {
    return label
      .replace('&laquo;', '«')
      .replace('&raquo;', '»')
      .replace(/&nbsp;/g, ' ')
  }
  return label
}
const prevLabel = computed(() => normalizeLabel(prevLink.value?.label ?? '«'))
const nextLabel = computed(() => normalizeLabel(nextLink.value?.label ?? '»'))

const go = (url) => {
  if (!url) return
  router.visit(url, { preserveScroll: true })
}
</script>