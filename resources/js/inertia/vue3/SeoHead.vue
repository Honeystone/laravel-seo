<script lang="ts">
import { Head, usePage } from "@inertiajs/vue3";
import { computed, defineComponent, h, type PropType } from "vue";

import { buildSeoTags, type SeoPayload } from "../shared/seo";

export default defineComponent({
  name: "SeoHead",
  props: {
    defaultTitle: { type: String as PropType<string | undefined>, default: undefined },
    forceNoIndex: { type: Boolean, default: false },
  },
  setup(props) {
    const page = usePage<{ seoPayload?: SeoPayload }>();

    const tags = computed(() =>
      buildSeoTags(page.props.seoPayload, {
        defaultTitle: props.defaultTitle,
        forceNoIndex: props.forceNoIndex,
      }),
    );

    return () =>
      tags.value.length === 0
        ? null
        : h(Head, null, () =>
            tags.value.map(({ tag, key, attrs, text }) => h(tag, { key, "head-key": key, ...attrs }, text)),
          );
  },
});
</script>
