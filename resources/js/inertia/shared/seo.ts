type WithConfig<T> = T & { config?: Partial<T> };

type CustomTags = Record<string, string | string[]>[];

export type MetaData = {
    title?: string;
    titleTemplate?: string;
    description?: string;
    keywords?: string[];
    canonicalEnabled?: boolean;
    canonical?: string;
    robots?: string[];
    custom?: CustomTags;
};

type MediaProperties = {
    url?: string | null;
    alt?: string | null;
    width?: string | null;
    height?: string | null;
    secureUrl?: string | null;
    type?: string | null;
};

type ProfileData = {
    type: "profile";
    firstName?: string;
    lastName?: string;
    username?: string;
    gender?: string;
};

type ArticleData = {
    type: "article";
    publishedTime?: string;
    modifiedTime?: string;
    expirationTime?: string;
    author?: ProfileData | ProfileData[];
    section?: string;
    tag?: string | string[];
};

type BookData = {
    type: "book";
    author?: ProfileData | ProfileData[];
    isbn?: string;
    releaseDate?: string;
    tag?: string | string[];
};

type OpenGraphType = string | ProfileData | ArticleData | BookData;

type AppCardData = {
    name: "app";
    iphoneId?: string;
    iphoneName?: string;
    iphoneUrl?: string;
    ipadId?: string;
    ipadName?: string;
    ipadUrl?: string;
    googlePlayId?: string;
    googlePlayName?: string;
    googlePlayUrl?: string;
    country?: string;
};

type PlayerCardData = {
    name: "player";
    player?: string;
    width?: string;
    height?: string;
    stream?: string;
};

type TwitterCard = string | AppCardData | PlayerCardData;

export type OpenGraphData = {
    enabled?: boolean;
    site?: string;
    type?: OpenGraphType;
    title?: string;
    description?: string;
    images?: (string | MediaProperties)[];
    audio?: (string | MediaProperties)[];
    videos?: (string | MediaProperties)[];
    determiner?: string;
    url?: string;
    locale?: string;
    alternateLocales?: string[];
    custom?: CustomTags;
};

export type TwitterData = {
    enabled?: boolean;
    site?: string;
    card?: TwitterCard;
    creator?: string;
    creatorId?: string;
    title?: string;
    description?: string;
    image?: string;
    imageAlt?: string;
};

export type JsonLdData = {
    enabled?: boolean;
    generated?: string;
    nonce?: string;
};

export type SeoPayload = {
    meta?: WithConfig<MetaData>;
    twitter?: WithConfig<TwitterData>;
    "open-graph"?: WithConfig<OpenGraphData>;
    "json-ld"?: WithConfig<JsonLdData>;
};

export type SeoOptions = {
    defaultTitle?: string;
    forceNoIndex?: boolean;
};

export type HeadTag = {
    tag: "title" | "meta" | "link" | "script";
    key: string;
    attrs: Record<string, string | undefined>;
    text?: string;
};

function resolve<T extends object>(data?: WithConfig<T>): T {
    const { config, ...rest } = data ?? ({} as WithConfig<T>);

    return { ...(config ?? {}), ...rest } as T;
}

const imageMap = {
    url: "og:image",
    alt: "og:image:alt",
    width: "og:image:width",
    height: "og:image:height",
    secureUrl: "og:image:secure_url",
    type: "og:image:type",
};

const videoMap = {
    url: "og:video",
    alt: "og:video:alt",
    width: "og:video:width",
    height: "og:video:height",
    secureUrl: "og:video:secure_url",
    type: "og:video:type",
};

const audioMap = {
    url: "og:audio",
    secureUrl: "og:audio:secure_url",
    type: "og:audio:type",
};

// prevents a `</script>` inside a value from terminating the JSON-LD script tag
const escapeJsonLd = (json: string) => json.replace(/</g, "\\u003c");

const list = <T>(value?: T | T[]): T[] => ([] as T[]).concat(value ?? []);

export function buildSeoTags(seo: SeoPayload | undefined, options: SeoOptions = {}): HeadTag[] {
    if (!seo) {
        return [];
    }

    const meta = resolve<MetaData>(seo.meta);
    const og = resolve<OpenGraphData>(seo["open-graph"]);
    const tw = resolve<TwitterData>(seo.twitter);
    const ld = resolve<JsonLdData>(seo["json-ld"]);

    const tags: HeadTag[] = [];

    const tag = (key: string, attribute: "name" | "property", name: string, content?: string | number | null) => {
        if (content === undefined || content === null || content === "") {
            return;
        }

        tags.push({ tag: "meta", key, attrs: { [attribute]: name, content: String(content) } });
    };

    const custom = (prefix: string, attribute: "name" | "property", groups?: CustomTags) => {
        (groups ?? []).forEach((group, i) => {
            Object.entries(group).forEach(([name, value]) => {
                list(value).forEach((content, j) => {
                    tag(`${prefix}-${i}-${name}-${j}`, attribute, name, content);
                });
            });
        });
    };

    // structured properties must directly follow their parent tag, so emit per item
    const media = (prefix: string, items: (string | MediaProperties)[] | undefined, map: Record<string, string>) => {
        (items ?? []).forEach((item, i) => {
            const properties: MediaProperties = typeof item === "string" ? { url: item } : item;

            Object.entries(map).forEach(([field, name]) => {
                tag(`${prefix}-${i}-${field}`, "property", name, properties[field as keyof MediaProperties]);
            });
        });
    };

    const profile = (key: string, alias: string, data: ProfileData) => {
        tag(`${key}-first`, "property", `${alias}:first_name`, data.firstName);
        tag(`${key}-last`, "property", `${alias}:last_name`, data.lastName);
        tag(`${key}-username`, "property", `${alias}:username`, data.username);
        tag(`${key}-gender`, "property", `${alias}:gender`, data.gender);
    };

    const authors = (alias: string, author?: ProfileData | ProfileData[]) => {
        list(author).forEach((data, i) => profile(`${alias}-${i}`, alias, data));
    };

    const typeTags = (alias: string, value?: string | string[]) => {
        list(value).forEach((content, i) => tag(`${alias}-tag-${i}`, "property", `${alias}:tag`, content));
    };

    const objectType = (type: ProfileData | ArticleData | BookData) => {
        switch (type.type) {
            case "article":
                tag("article:published_time", "property", "article:published_time", type.publishedTime);
                tag("article:modified_time", "property", "article:modified_time", type.modifiedTime);
                tag("article:expiration_time", "property", "article:expiration_time", type.expirationTime);
                authors("article:author", type.author);
                tag("article:section", "property", "article:section", type.section);
                typeTags("article", type.tag);
                break;
            case "book":
                authors("book:author", type.author);
                tag("book:isbn", "property", "book:isbn", type.isbn);
                tag("book:release_date", "property", "book:release_date", type.releaseDate);
                typeTags("book", type.tag);
                break;
            case "profile":
                profile("profile", "profile", type);
                break;
        }
    };

    /* ---- META ---- */
    const title = meta.title
        ? meta.titleTemplate
            ? meta.titleTemplate.replace("{title}", meta.title)
            : meta.title
        : options.defaultTitle;

    if (title) {
        tags.push({ tag: "title", key: "title", attrs: {}, text: title });
    }

    tag("description", "name", "description", meta.description);

    if (meta.keywords?.length) {
        tag("keywords", "name", "keywords", meta.keywords.join(","));
    }

    if (meta.canonicalEnabled && meta.canonical) {
        tags.push({ tag: "link", key: "canonical", attrs: { rel: "canonical", href: meta.canonical } });
    }

    if (options.forceNoIndex) {
        tag("robots", "name", "robots", "noindex,nofollow");
    } else if (meta.robots?.length) {
        tag("robots", "name", "robots", meta.robots.join(","));
    }

    custom("meta-custom", "name", meta.custom);

    /* ---- OPEN GRAPH ---- */
    if (og.enabled) {
        tag("og:site_name", "property", "og:site_name", og.site);
        tag("og:type", "property", "og:type", typeof og.type === "string" ? og.type : og.type?.type);
        tag("og:title", "property", "og:title", og.title);
        tag("og:description", "property", "og:description", og.description);
        media("og-image", og.images, imageMap);
        tag("og:url", "property", "og:url", og.url);
        media("og-audio", og.audio, audioMap);
        media("og-video", og.videos, videoMap);
        tag("og:determiner", "property", "og:determiner", og.determiner);
        tag("og:locale", "property", "og:locale", og.locale);
        (og.alternateLocales ?? []).forEach((locale, i) => {
            tag(`og:locale:alternate-${i}`, "property", "og:locale:alternate", locale);
        });

        if (og.type && typeof og.type === "object") {
            objectType(og.type);
        }
    }

    custom("og-custom", "property", og.custom);

    /* ---- TWITTER ---- */
    if (tw.enabled) {
        tag(
            "twitter:card",
            "name",
            "twitter:card",
            typeof tw.card === "string" ? tw.card : (tw.card?.name ?? "summary_large_image"),
        );

        if (tw.card && typeof tw.card === "object") {
            const card = tw.card;

            if (card.name === "app") {
                const apps: [string, string | undefined][] = [
                    ["id:iphone", card.iphoneId],
                    ["name:iphone", card.iphoneName],
                    ["url:iphone", card.iphoneUrl],
                    ["id:ipad", card.ipadId],
                    ["name:ipad", card.ipadName],
                    ["url:ipad", card.ipadUrl],
                    ["id:googleplay", card.googlePlayId],
                    ["name:googleplay", card.googlePlayName],
                    ["url:googleplay", card.googlePlayUrl],
                ];

                apps.forEach(([suffix, value]) => tag(`twitter:app:${suffix}`, "name", `twitter:app:${suffix}`, value));
            } else {
                tag("twitter:player", "name", "twitter:player", card.player);
                tag("twitter:player:width", "name", "twitter:player:width", card.width);
                tag("twitter:player:height", "name", "twitter:player:height", card.height);
                tag("twitter:player:stream", "name", "twitter:player:stream", card.stream);
            }
        }

        tag("twitter:site", "name", "twitter:site", tw.site);
        tag("twitter:creator", "name", "twitter:creator", tw.creator);
        tag("twitter:creator:id", "name", "twitter:creator:id", tw.creatorId);
        tag("twitter:title", "name", "twitter:title", tw.title);
        tag("twitter:description", "name", "twitter:description", tw.description);
        tag("twitter:image", "name", "twitter:image", tw.image);

        if (tw.image) {
            tag("twitter:image:alt", "name", "twitter:image:alt", tw.imageAlt);
        }
    }

    /* ---- JSON-LD ---- */
    if (ld.enabled && ld.generated) {
        tags.push({
            tag: "script",
            key: "json-ld",
            attrs: { type: "application/ld+json", nonce: ld.nonce || undefined },
            text: escapeJsonLd(ld.generated),
        });
    }

    return tags;
}
