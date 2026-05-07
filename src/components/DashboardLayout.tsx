"use client";

import { useState, useEffect } from "react";
import { usePathname, useRouter } from "next/navigation";
import Link from "next/link";

type User = {
  id: number;
  nome: string;
  role: string;
  foto: string | null;
};

export default function DashboardLayout({
  children,
  title,
}: {
  children: React.ReactNode;
  title?: string;
}) {
  const pathname = usePathname();
  const router = useRouter();
  const [user, setUser] = useState<User | null>(null);

  useEffect(() => {
    fetch("/api/auth/me")
      .then((res) => res.json())
      .then((data) => {
        if (data.authenticated) setUser(data.user);
      });
  }, []);

  async function handleLogout() {
    await fetch("/api/auth/logout", { method: "POST" });
    router.push("/");
  }

  const isActive = (path: string) => pathname.startsWith(path);

  const navItems = [
    { href: "/dashboard", label: "Home", icon: HomeIcon, match: "/dashboard" },
    { href: "/gatos", label: "Gatos", icon: CatIcon, match: "/gatos" },
    { href: "/saude", label: "Saúde", icon: HealthIcon, match: ["/saude", "/medicamentos"] },
    { href: "/financeiro", label: "Finanças", icon: FinanceIcon, match: "/financeiro" },
    { href: "/perfil", label: "Perfil", icon: ProfileIcon, match: "/perfil" },
  ];

  const firstName = user?.nome?.split(" ")[0] || "Usuário";

  return (
    <div className="min-h-screen bg-gray-50 pb-20">
      {/* Header */}
      <header className="bg-gradient-to-br from-brand-500 to-brand-600 text-white shadow-brand rounded-b-[2.5rem] pt-10 pb-14 px-6 mb-[-40px] relative z-0">
        <div className="absolute top-10 right-6 flex items-center gap-2">
          <button
            onClick={handleLogout}
            className="p-2.5 bg-white/20 backdrop-blur-md rounded-2xl hover:bg-white/30 transition-all active:scale-90 border border-white/30"
            title="Sair"
          >
            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
          </button>
        </div>
        <h1 className="text-3xl font-outfit font-extrabold tracking-tight text-center leading-tight">
          {title || `Olá, ${firstName} 👋`}
        </h1>
      </header>

      {/* Main Content */}
      <main className="container mx-auto px-4 relative z-10 fade-in">
        {children}
      </main>

      {/* Bottom Navigation */}
      <nav className="fixed bottom-0 left-0 w-full z-50 px-2 border-none"
        style={{
          background: "rgba(255, 255, 255, 0.75)",
          backdropFilter: "blur(12px)",
          WebkitBackdropFilter: "blur(12px)",
          borderTop: "1px solid rgba(255, 255, 255, 0.3)",
          boxShadow: "0 -1px 20px rgba(0,0,0,0.05)",
        }}>
        <div className="flex justify-around items-center h-18 max-w-lg mx-auto py-2">
          {navItems.map((item) => {
            const active = Array.isArray(item.match)
              ? item.match.some((p) => isActive(p))
              : isActive(item.match);
            return (
              <Link
                key={item.href}
                href={item.href}
                className={`flex flex-col items-center justify-center w-full transition-all duration-300 active:scale-90 ${active ? "text-brand-600" : "text-gray-400"}`}
              >
                <div className={`relative p-1.5 ${active ? "bg-brand-50 rounded-xl" : ""}`}>
                  <item.icon active={active} />
                </div>
                <span className="text-[10px] font-bold uppercase tracking-wider">
                  {item.label}
                </span>
              </Link>
            );
          })}
        </div>
      </nav>
    </div>
  );
}

function HomeIcon({ active }: { active: boolean }) {
  return (
    <svg className="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.2} d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
    </svg>
  );
}

function CatIcon({ active }: { active: boolean }) {
  return (
    <svg className="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.2} d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
    </svg>
  );
}

function HealthIcon({ active }: { active: boolean }) {
  return (
    <svg className="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
  );
}

function FinanceIcon({ active }: { active: boolean }) {
  return (
    <svg className="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
  );
}

function ProfileIcon({ active }: { active: boolean }) {
  return (
    <svg className="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
    </svg>
  );
}
