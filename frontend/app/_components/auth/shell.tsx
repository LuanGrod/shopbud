import { BrandMark } from "../brand-mark";

type AuthShellProps = {
  children: React.ReactNode;
};

export function AuthShell({ children }: AuthShellProps) {
  return (
    <main className="min-h-screen bg-brand-graphite">
      <div className="mx-auto flex min-h-screen w-full max-w-[430px] flex-col bg-background px-5 pb-8 pt-12">
        <div className="flex justify-center">
          <BrandMark className="h-auto w-52" priority />
        </div>
        <div className="flex flex-1 flex-col justify-center">{children}</div>
      </div>
    </main>
  );
}
