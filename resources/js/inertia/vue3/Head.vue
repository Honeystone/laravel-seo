<script>
import {Head} from '@inertiajs/vue3';

export default {
  extends: Head,
  methods: {
    addTitleElement(elements) {
      //the seo packages has got this
      return elements
    },
    getMetadata() {
      const metadata = this.$page.props.seo;

      const element = new DOMParser().parseFromString(
          `<div>${metadata}</div>`,
          'text/html',
      ).body.firstChild;

      return Array.from(element.children);
    },
    renderElements(elements) {
      return elements.map((element) => {
        element.setAttribute('inertia', '');
        return element.outerHTML;
      });
    },
  },
  render() {
    const seoElements = this.renderElements(this.getMetadata());

    const headElements = this.renderNodes(
        this.$slots.default ? this.$slots.default() : [],
    );

    this.provider.update([...seoElements, ...headElements]);
  },
}
</script>
