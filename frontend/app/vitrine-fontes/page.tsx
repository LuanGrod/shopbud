import Image from "next/image";
import logo from "@public/wordmark.svg";

const sampleText = "Compra sem confusão";
const bodyText =
    "Lista de compras por setores, totais em tempo real e leitura confortável durante o mercado.";

export default function Page() {
    return (
        <main className="min-h-screen bg-background px-5 py-8 text-foreground">
            <div className="mx-auto flex w-full max-w-5xl flex-col gap-8">
                <header className="flex flex-col gap-4 border-b border-border pb-8">
                    <Image src={logo} alt="ShopBud" height={54} priority />
                    <div className="max-w-2xl">
                        <p className="font-sans text-sm font-medium text-muted-foreground">
                            Vitrine de tipografia
                        </p>
                        <h1 className="mt-2 font-heading text-4xl font-bold text-primary">
                            Fontes do ShopBud
                        </h1>
                        <p className="mt-3 max-w-xl font-sans text-base leading-7 text-muted-foreground">
                            Comparativo rápido entre a fonte de corpo, títulos e marca.
                        </p>
                    </div>
                </header>

                <section className="grid gap-4 md:grid-cols-3">
                    <FontSample
                        label="Corpo"
                        token="font-sans"
                        family="SF Pro Text"
                        className="font-sans"
                    />
                    <FontSample
                        label="Títulos"
                        token="font-heading"
                        family="SF Pro Display"
                        className="font-heading"
                    />
                    <FontSample
                        label="Marca"
                        token="font-brand"
                        family="Baloo 2"
                        className="font-brand"
                    />
                </section>

                <section className="grid gap-4 md:grid-cols-2">
                    <div className="rounded-xl border border-border bg-surface p-5">
                        <p className="font-sans text-sm font-medium text-muted-foreground">
                            Exemplo de card com hierarquia real
                        </p>
                        <h2 className="mt-3 font-heading text-3xl font-bold text-foreground">
                            Minha lista
                        </h2>
                        <p className="mt-2 font-sans text-base leading-7 text-muted-foreground">
                            {bodyText}
                        </p>
                        <div className="mt-5 flex items-center justify-between rounded-lg bg-secondary px-4 py-3">
                            <span className="font-sans text-sm font-medium text-secondary-foreground">
                                Total estimado
                            </span>
                            <span className="font-heading text-2xl font-bold text-primary">
                                R$ 128,40
                            </span>
                        </div>
                    </div>

                    <div className="rounded-xl border border-border bg-primary p-5 text-primary-foreground">
                        <p className="font-sans text-sm font-medium opacity-80">
                            Uso da fonte de marca
                        </p>
                        <p className="mt-3 font-brand text-5xl font-bold">
                            ShopBud
                        </p>
                        <p className="mt-3 font-heading text-2xl font-semibold">
                            {sampleText}
                        </p>
                        <p className="mt-2 font-sans text-base leading-7 opacity-85">
                            Baloo 2 fica reservada para momentos expressivos, enquanto SF Pro
                            segura leitura e interface.
                        </p>
                    </div>
                </section>
            </div>
        </main>
    );
}

function FontSample({
    label,
    token,
    family,
    className,
}: {
    label: string;
    token: string;
    family: string;
    className: string;
}) {
    return (
        <article className="rounded-xl border border-border bg-surface p-5">
            <div className="flex items-start justify-between gap-3">
                <div>
                    <p className="font-sans text-sm font-medium text-muted-foreground">
                        {label}
                    </p>
                    <p className="font-sans text-xs text-muted-foreground">{token}</p>
                </div>
                <span className="rounded-full bg-secondary px-3 py-1 font-sans text-xs font-medium text-secondary-foreground">
                    {family}
                </span>
            </div>

            <p className={`mt-5 text-4xl font-bold text-foreground ${className}`}>
                {sampleText}
            </p>
            <p className={`mt-4 text-base leading-7 text-muted-foreground ${className}`}>
                Frutas, verduras, laticínios e mercearia organizados na ordem do
                supermercado.
            </p>
            <div className="mt-5 grid grid-cols-2 gap-3">
                <p className={`text-xl font-normal ${className}`}>Regular 400</p>
                <p className={`text-xl font-bold ${className}`}>Bold 700</p>
                <p className={`text-sm ${className}`}>1234567890</p>
                <p className={`text-sm font-semibold ${className}`}>R$ 128,40</p>
            </div>
        </article>
    );
}
