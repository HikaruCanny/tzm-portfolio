import { h, type DefineComponent, type VNode, type VNodeChild } from 'vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

type PageModule = {
    default: DefineComponent & {
        layout?: unknown;
    };
};

/**
 * Settings pages currently declare layout breadcrumbs as a plain object.
 * Inertia can only render a component, so wrap those pages in the real layouts.
 */
export function withAppLayouts(name: string, module: PageModule): PageModule {
    if (!name.startsWith('settings/')) {
        return module;
    }

    const page = module.default;
    const declaredLayout = page.layout;
    const breadcrumbs =
        declaredLayout !== null &&
        typeof declaredLayout === 'object' &&
        !Array.isArray(declaredLayout) &&
        'breadcrumbs' in declaredLayout
            ? declaredLayout.breadcrumbs
            : [];

    page.layout = (_render: typeof h, child: VNode): VNodeChild =>
        h(AppLayout, { breadcrumbs }, () => h(SettingsLayout, () => child));

    return module;
}
