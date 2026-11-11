import { Head } from '@inertiajs/react';
import { AssistantBanner } from '@/components/directory/assistant-banner';
import { ClosingCta } from '@/components/directory/closing-cta';
import { FeatureCards } from '@/components/directory/feature-cards';
import { HomeHero } from '@/components/directory/home-hero';
import { HowItWorks } from '@/components/directory/how-it-works';
import { TrustStrip } from '@/components/directory/trust-strip';
import type { ShopSummary } from '@/types';

export default function Home({ shops }: { shops: ShopSummary[] }) {
    const openCount = shops.filter(
        (shop) => shop.openState !== 'closed',
    ).length;

    return (
        <>
            <Head title="Cari tempat printing di CitraLand" />
            <main className="flex flex-col gap-14 pb-4">
                <HomeHero shops={shops} openCount={openCount} />
                <div className="flex flex-col gap-14 px-4">
                    <FeatureCards />
                    <HowItWorks />
                    <TrustStrip />
                    <AssistantBanner />
                    <ClosingCta />
                </div>
            </main>
        </>
    );
}
