import { PageHeader } from "@/app/_components/page-header";
import { PlaceholderPanel } from "@/app/_components/ui";
import { LogoutButton } from "@/app/_components/logout-button";

export default function ConfiguracoesPage() {
  return (
    <div className="space-y-6">
      <PageHeader
        title="Configurações"
        subtitle="Gerencie sua conta e preferências do aplicativo."
      />

      <section className="divide-y divide-border rounded-xl border border-border bg-surface shadow-[0_2px_8px_rgba(0,0,0,0.08)]">
        {["Conta", "Alterar senha", "Sobre o app"].map((item) => (
          <div key={item} className="min-h-12 px-4 py-3 text-sm font-medium">
            {item}
          </div>
        ))}
        <LogoutButton />
      </section>

      <PlaceholderPanel
        title="Sessão"
        description="O logout encerra a sessão local e remove o cookie seguro do app."
      />
    </div>
  );
}
