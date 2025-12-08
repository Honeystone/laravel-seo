// resources/js/components/SeoHead.tsx
import { Head, usePage } from '@inertiajs/react';

type HreflangLink = {
    href: string;
    hreflang: string;
};

type MetaConfig = {
    title?: string;
    titleTemplate?: string;
    description?: string;
    canonicalEnabled?: boolean;
};

type MetaData = {
    title?: string;
    description?: string;
    canonical?: string;
    robots?: string | string[];
    keywords?: string[];
    hreflangs?: HreflangLink[];
    config?: MetaConfig;
};

type OgImage = {
    url: string;
    alt?: string;
    width?: number;
    height?: number;
};

type OpenGraphData = {
    title?: string;
    description?: string;
    type?: string;
    url?: string;
    site?: string;
    locale?: string;
    images?: (OgImage | string)[];
};

type TwitterData = {
    card?: string;
    site?: string;
    creator?: string;
    title?: string;
    description?: string;
    image?: string;
};

type IconLink = {
    rel: string;
    href: string;
    type?: string;
    sizes?: string;
};

type JsonLdEntry = {
    generated?: string;
    [key: string]: unknown;
} | string | null;

type SeoObject = {
    meta?: MetaData;
    twitter?: TwitterData;
    ['open-graph']?: OpenGraphData;
    ['json-ld']?: JsonLdEntry | JsonLdEntry[];
    icons?: IconLink[];
};

type SeoHeadProps = {
    defaultTitleSuffix?: string;
    forceNoIndex?: boolean;
};

export default function SeoHead({ defaultTitleSuffix, forceNoIndex = false }: SeoHeadProps) {
    const { props } = usePage<{ seoPayload?: SeoObject }>();
    const seo = props.seoPayload;

    if (!seo) {
        return null;
    }

    const meta = seo.meta ?? {};
    const og = (seo['open-graph'] ?? {}) as OpenGraphData;
    const tw = seo.twitter ?? {};
    const ld = seo['json-ld'];
    const icons = seo.icons ?? [];

    const ogImages: OgImage[] = (og.images ?? []).map((image) =>
        typeof image === 'string'
            ? { url: image }
            : image,
    );

    const template = ''; // meta.config?.titleTemplate;
    const baseTitle = meta.title ?? og.title ?? tw.title ?? meta.config?.title ?? defaultTitleSuffix;
    const fullTitle =
        meta.title && template
            ? template.replace('{title}', meta.title)
            : baseTitle;

    const description =
        meta.description ??
        og.description ??
        tw.description ??
        meta.config?.description ??
        undefined;

    const canonical = meta.canonical ?? og.url ?? undefined;

    const robotsSource = Array.isArray(meta.robots)
        ? meta.robots.filter(Boolean).join(', ')
        : meta.robots;

    const robotsContent = forceNoIndex ? 'noindex,nofollow' : robotsSource ?? undefined;

    const keywords =
        meta.keywords && meta.keywords.length > 0
            ? meta.keywords.join(', ')
            : undefined;

    const jsonLdEntries = Array.isArray(ld) ? ld : ld ? [ld] : [];
    const jsonLdPayload = jsonLdEntries
        .map((entry) => {
            if (!entry) {
                return null;
            }

            if (typeof entry === 'string') {
                return entry;
            }

            if (typeof entry.generated === 'string') {
                return entry.generated;
            }

            const rest = { ...(entry as Record<string, unknown>) };
            delete rest.config;
            return JSON.stringify(rest);
        })
        .filter(Boolean) as string[];

    return (
        <Head>
            {/* ---- BASIC ---- */}
            {fullTitle && <title>{fullTitle}</title>}

            {description && (
                <meta name="description" head-key="description" content={description} />
            )}

            {canonical && (
                <link rel="canonical" head-key="canonical" href={canonical} />
            )}

            {robotsContent && (
                <meta name="robots" content={robotsContent} />
            )}

            {keywords && (
                <meta name="keywords" content={keywords} />
            )}

            {/* ---- HREFLANG ---- */}
            {meta.hreflangs?.map((link, idx) => (
                <link
                    key={`hreflang-${idx}`}
                    rel="alternate"
                    hrefLang={link.hreflang}
                    href={link.href}
                />
            ))}

            {/* ---- ICONS / FAVICONS ---- */}
            {icons.map((icon, idx) => (
                <link
                    key={`icon-${idx}`}
                    rel={icon.rel}
                    href={icon.href}
                    type={icon.type}
                    sizes={icon.sizes}
                />
            ))}

            {/* ---- OPEN GRAPH ---- */}
            {canonical && (
                <meta property="og:url" content={canonical} />
            )}
            {fullTitle && (
                <meta property="og:title" content={og.title ?? fullTitle} />
            )}
            {description && (
                <meta
                    property="og:description"
                    content={og.description ?? description}
                />
            )}
            {og.site && (
                <meta property="og:site_name" content={og.site} />
            )}
            {og.type && (
                <meta property="og:type" content={og.type} />
            )}
            {og.locale && (
                <meta property="og:locale" content={og.locale} />
            )}

            {ogImages.map((image, idx) => (
                <meta
                    key={`og-image-${idx}`}
                    property="og:image"
                    content={image.url}
                />
            ))}
            {ogImages.map((image, idx) =>
                image.alt ? (
                    <meta
                        key={`og-image-alt-${idx}`}
                        property="og:image:alt"
                        content={image.alt}
                    />
                ) : null
            )}
            {ogImages.map((image, idx) =>
                image.width ? (
                    <meta
                        key={`og-image-width-${idx}`}
                        property="og:image:width"
                        content={String(image.width)}
                    />
                ) : null
            )}
            {ogImages.map((image, idx) =>
                image.height ? (
                    <meta
                        key={`og-image-height-${idx}`}
                        property="og:image:height"
                        content={String(image.height)}
                    />
                ) : null
            )}

            {/* ---- TWITTER ---- */}
            <meta
                name="twitter:card"
                content={tw.card ?? 'summary_large_image'}
            />
            {tw.site && (
                <meta name="twitter:site" content={tw.site} />
            )}
            {tw.creator && (
                <meta name="twitter:creator" content={tw.creator} />
            )}
            {fullTitle && (
                <meta
                    name="twitter:title"
                    content={tw.title ?? fullTitle}
                />
            )}
            {description && (
                <meta
                    name="twitter:description"
                    content={tw.description ?? description}
                />
            )}
            {(tw.image || ogImages[0]) && (
                <meta
                    name="twitter:image"
                    content={tw.image ?? ogImages[0]?.url}
                />
            )}

            {/* ---- JSON-LD ---- */}
            {jsonLdPayload.map((schema, idx) => (
                <script
                    key={`ld-json-${idx}`}
                    type="application/ld+json"
                    dangerouslySetInnerHTML={{
                        __html: schema,
                    }}
                />
            ))}
        </Head>
    );
}
