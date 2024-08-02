<script>
import {Head} from '@inertiajs/vue3';

export default {
  extends: Head,
  data() {
    return {
      provider: this.$headManager.createProvider(),
    };
  },
  beforeUnmount() {
    this.provider.disconnect()
  },
  methods: {
    addTitleElement(elements) {
      //the seo packages has go this
      return elements
    },
  },
  render() {
    const metadata = this.$page.props.seo;

    const seo = new DOMParser().parseFromString(
        `<div>${metadata}</div>`,
        'text/html',
    ).body.firstChild;

    const seoElements = Array.from(seo.children).map((element) => {
      element.setAttribute('inertia', '');
      return element.outerHTML;
    });

    const headElements = this.$slots.default ? this.$slots.default() : [];

    this.provider.update([
      ...seoElements,
      ...this.renderNodes(headElements),
    ]);
  },
}
</script>
