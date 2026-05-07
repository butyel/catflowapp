"use client";

import { useState, useEffect } from "react";
import { useRouter } from "next/navigation";
import DashboardLayout from "@/components/DashboardLayout";

type DashboardData = {
  totalGatos: number;
  gatosTratamento: number;
  medicamentosAtivos: number;
  estoqueBaixo: number;
  vacinasPendentes: number;
  totalReceitas: number;
  totalDespesas: number;
  alerts: Array<{ id: number; titulo: string; descricao: string; data: string }>;
};

export default function DashboardPage() {
  const router = useRouter();
  const [data, setData] = useState<DashboardData | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    async function load() {
      try {
        const res = await fetch("/api/dashboard/stats");
        const json = await res.json();
        if (json.success) setData(json.data);
      } catch (e) {
        console.error("Error loading dashboard", e);
      } finally {
        setLoading(false);
      }
    }
    load();
  }, []);

  if (loading) {
    return (
      <DashboardLayout>
        <div className="space-y-8 -mt-10">
          <div className="grid grid-cols-2 gap-4">
            {[1, 2].map((i) => (
              <div key={i} className="bg-white rounded-3xl p-5 shadow-premium border border-white">
                <div className="shimmer h-14 w-14 rounded-2xl mb-3" />
                <div className="shimmer h-8 w-20 mb-1" />
                <div className="shimmer h-3 w-24" />
              </div>
            ))}
          </div>
        </div>
      </DashboardLayout>
    );
  }

  const total = (data?.totalReceitas || 0) + (data?.totalDespesas || 0);
  const pctReceita = total > 0 ? ((data?.totalReceitas || 0) / total) * 100 : 50;
  const pctDespesa = total > 0 ? ((data?.totalDespesas || 0) / total) * 100 : 50;

  return (
    <DashboardLayout>
      <div className="space-y-8 -mt-10">
        {/* Quick Stats Cards */}
        <div className="grid grid-cols-2 gap-4">
          <div className="premium-card bg-white rounded-3xl p-5 shadow-premium border border-white flex flex-col items-center justify-center text-center">
            <div className="w-14 h-14 bg-brand-50 text-brand-500 rounded-2xl flex items-center justify-center mb-3 shadow-inner">
              <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" />
              </svg>
            </div>
            <h3 className="text-3xl font-outfit font-extrabold text-gray-800">{data?.totalGatos ?? "-"}</h3>
            <p className="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Gatos Ativos</p>
          </div>
          <div className="premium-card bg-white rounded-3xl p-5 shadow-premium border border-white flex flex-col items-center justify-center text-center">
            <div className="w-14 h-14 bg-blue-50 text-blue-500 rounded-2xl flex items-center justify-center mb-3 shadow-inner">
              <svg className="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
              </svg>
            </div>
            <h3 className="text-3xl font-outfit font-extrabold text-gray-800">{data?.gatosTratamento ?? "-"}</h3>
            <p className="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Tratamentos</p>
          </div>
        </div>

        {/* Additional Stats Row */}
        <div className="grid grid-cols-3 gap-3">
          <div className="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
            <p className="text-xl font-extrabold text-green-500">{data?.medicamentosAtivos ?? "-"}</p>
            <p className="text-[9px] text-gray-400 font-bold uppercase">Medicamentos</p>
          </div>
          <div className="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
            <p className="text-xl font-extrabold text-purple-500">{data?.estoqueBaixo ?? "-"}</p>
            <p className="text-[9px] text-gray-400 font-bold uppercase">Estoque Baixo</p>
          </div>
          <div className="bg-white rounded-2xl p-4 shadow-sm border border-gray-100 text-center">
            <p className="text-xl font-extrabold text-yellow-500">{data?.vacinasPendentes ?? "-"}</p>
            <p className="text-[9px] text-gray-400 font-bold uppercase">Vacinas Pend.</p>
          </div>
        </div>

        {/* Alerts */}
        {data?.alerts && data.alerts.length > 0 && (
          <div className="space-y-4">
            <h2 className="text-xl font-outfit font-bold text-gray-800 px-1">Aproximando agora... ⚠️</h2>
            <div className="space-y-2">
              {data.alerts.map((alert) => (
                <div key={alert.id} className="p-3 bg-red-50 rounded-xl border border-red-100">
                  <p className="font-bold text-gray-800 text-sm">{alert.titulo}</p>
                  <p className="text-xs text-gray-500">{alert.descricao}</p>
                </div>
              ))}
            </div>
          </div>
        )}

        {/* Financial Overview */}
        <div className="bg-white rounded-[2rem] shadow-premium p-6 border border-white">
          <div className="flex justify-between items-center mb-6">
            <h2 className="text-xl font-outfit font-bold text-gray-800">
              Financeiro <span className="text-gray-400 font-medium text-sm">(Mês)</span>
            </h2>
            <button
              onClick={() => router.push("/financeiro")}
              className="bg-brand-50 text-brand-600 px-4 py-1.5 rounded-full text-xs font-bold hover:bg-brand-100 transition-colors"
            >
              Ver tudo
            </button>
          </div>

          <div className="flex justify-between mb-6">
            <div>
              <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Entradas</p>
              <p className="text-2xl font-outfit font-extrabold text-green-500">
                {formatCurrency(data?.totalReceitas || 0)}
              </p>
            </div>
            <div className="text-right">
              <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Saídas</p>
              <p className="text-2xl font-outfit font-extrabold text-red-500">
                {formatCurrency(data?.totalDespesas || 0)}
              </p>
            </div>
          </div>

          <div className="w-full bg-gray-50 rounded-full h-4 mb-1 overflow-hidden flex border border-gray-100 p-0.5">
            <div
              className="bg-green-400 h-full rounded-full transition-all duration-1000"
              style={{ width: `${pctReceita}%` }}
            />
            <div
              className="bg-red-400 h-full rounded-full transition-all duration-1000"
              style={{ width: `${pctDespesa}%` }}
            />
          </div>

          <div className="flex justify-between mt-3 text-xs text-gray-400">
            <span>{pctReceita.toFixed(0)}%</span>
            <span>{pctDespesa.toFixed(0)}%</span>
          </div>
        </div>

        {/* Quick Links */}
        <div className="space-y-4">
          <h2 className="text-xl font-outfit font-bold text-gray-800 px-1">Gestão do Gatil</h2>
          <div className="grid grid-cols-3 gap-3 md:gap-4">
            <QuickLink href="/alas" icon={<AlasIcon />} label="Alas" color="blue" />
            <QuickLink href="/estoque" icon={<EstoqueIcon />} label="Estoque" color="green" />
            <QuickLink href="/dicas" icon={<DicasIcon />} label="Dicas" color="orange" />
            <QuickLink href="/adocoes" icon={<AdocoesIcon />} label="Adoções" color="purple" />
            <QuickLink href="/configuracoes" icon={<ConfigIcon />} label="Config" color="gray" />
            <QuickLink href="/ajuda" icon={<AjudaIcon />} label="Ajuda" color="gray" />
          </div>
        </div>

        {/* Lembretes Section */}
        <div className="bg-white rounded-[2rem] shadow-premium p-6 border border-white">
          <h2 className="text-xl font-outfit font-bold text-gray-800 mb-4">Lembretes 🔔</h2>
          <p className="text-center text-gray-400 text-sm py-4">Carregando...</p>
        </div>
      </div>
    </DashboardLayout>
  );
}

function QuickLink({
  href,
  icon,
  label,
  color,
}: {
  href: string;
  icon: React.ReactNode;
  label: string;
  color: string;
}) {
  const router = useRouter();
  const colorMap: Record<string, string> = {
    blue: "bg-blue-50 text-blue-500",
    green: "bg-green-50 text-green-500",
    orange: "bg-orange-50 text-orange-500",
    purple: "bg-purple-50 text-purple-500",
    gray: "bg-gray-50 text-gray-500",
  };

  return (
    <button
      onClick={() => router.push(href)}
      className="premium-card bg-white rounded-2xl md:rounded-3xl p-3 md:p-5 border border-white flex flex-col items-center justify-center shadow-premium group"
    >
      <div
        className={`w-10 h-10 md:w-12 md:h-12 ${colorMap[color]} rounded-xl md:rounded-2xl flex items-center justify-center mb-2 md:mb-3 group-hover:scale-110 transition-transform shadow-inner`}
      >
        {icon}
      </div>
      <span className="text-[8px] md:text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">
        {label}
      </span>
    </button>
  );
}

function AlasIcon() {
  return (
    <svg className="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
    </svg>
  );
}

function EstoqueIcon() {
  return (
    <svg className="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
    </svg>
  );
}

function DicasIcon() {
  return (
    <svg className="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.364-6.364l-.707-.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M12 12a3 3 0 100-6 3 3 0 000 6z" />
    </svg>
  );
}

function AdocoesIcon() {
  return (
    <svg className="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
  );
}

function ConfigIcon() {
  return (
    <svg className="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
    </svg>
  );
}

function AjudaIcon() {
  return (
    <svg className="w-5 h-5 md:w-6 md:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
    </svg>
  );
}

function formatCurrency(value: number): string {
  return value.toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
  });
}
