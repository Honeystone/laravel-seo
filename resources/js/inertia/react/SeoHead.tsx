import { Head, usePage } from "@inertiajs/react";

import { buildSeoTags, type SeoOptions, type SeoPayload } from "../shared/seo";

export default function SeoHead({ defaultTitle, forceNoIndex = false }: SeoOptions) {
    const { props } = usePage<{ seoPayload?: SeoPayload }>();
    const tags = buildSeoTags(props.seoPayload, { defaultTitle, forceNoIndex });

    if (tags.length === 0) {
        return null;
    }

    return (
        <Head>
            {tags.map(({ tag: Tag, key, attrs, text }) =>
                Tag === "script" ? (
                    <script key={key} head-key={key} {...attrs} dangerouslySetInnerHTML={{ __html: text ?? "" }} />
                ) : Tag === "title" ? (
                    <title key={key}>{text}</title>
                ) : (
                    <Tag key={key} head-key={key} {...attrs} />
                ),
            )}
        </Head>
    );
}
