import { BottomNavigation } from "./bottom-navigation";

export function AppShell({ children }: { children: React.ReactNode }) {
  return (
    <div className="min-h-screen bg-brand-graphite text-foreground">
      <div className="mx-auto flex min-h-screen w-full max-w-[430px] flex-col bg-background">
        <main className="flex-1 px-4 pb-6 pt-6">{children}</main>
        <BottomNavigation />
      </div>
    </div>
  );
}
