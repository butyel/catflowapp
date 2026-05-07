"use client";

import { useState, useEffect } from "react";
import { useRouter } from "next/navigation";
import DashboardLayout from "@/components/DashboardLayout";

type Gato = {
  id: number;
  nome: string;
  foto: string | null;
  sexo: string;
  raca: string | null;
  status: string;
  castrado: boolean;
  ala_nome: string | null;
  idade: string | null;
};

export default function GatosPage() {
  const router = useRouter();
  const [gatos, setGatos] = useState<Gato[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState("");
  const [page, setPage] = useState(1);
  const [totalPages, setTotalPages] = useState(1);

  async function loadGatos() {
    setLoading(true);
    try {
      const params = new URLSearchParams({ page: String(page) });
      if (search) params.set("search", search);
      const res = await fetch(`/api/gatos?${params}`);
      const json = await res.json();
      if (json.success) {
        setGatos(json.data);
        setTotalPages(json.pagination.total_pages);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadGatos();
  }, [page]);

  function handleSearch(e: React.FormEvent) {
    e.preventDefault();
    setPage(1);
    loadGatos();
  }

  const statusColors: Record<string, string> = {
    ativo: "bg-green-100 text-green-700",
    tratamento: "bg-yellow-100 text-yellow-700",
    adotado: "bg-blue-100 text-blue-700",
    Doado: "bg-purple-100 text-purple-700",
    aposentado: "bg-gray-100 text-gray-700",
    obito: "bg-red-100 text-red-700",
  };

  return (
    <DashboardLayout title="Meus Gatos 🐱">
      <div className="space-y-6 -mt-10">
        {/* Search and Add */}
        <div className="flex gap-3">
          <form onSubmit={handleSearch} className="flex-1">
            <input
              type="text"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
              placeholder="Buscar gatos..."
              className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all bg-white"
            />
          </form>
          <button
            onClick={() => router.push("/gatos/novo")}
            className="bg-brand-500 text-white px-4 py-3 rounded-xl font-bold hover:bg-brand-600 transition-all shadow-lg shadow-brand-200"
          >
            + Novo
          </button>
        </div>

        {/* Gatos List */}
        {loading ? (
          <div className="space-y-3">
            {[1, 2, 3].map((i) => (
              <div key={i} className="bg-white rounded-2xl p-4 shadow-premium border border-white flex items-center gap-4">
                <div className="shimmer w-14 h-14 rounded-xl" />
                <div className="flex-1 space-y-2">
                  <div className="shimmer h-5 w-32" />
                  <div className="shimmer h-3 w-24" />
                </div>
              </div>
            ))}
          </div>
        ) : gatos.length === 0 ? (
          <div className="text-center py-16">
            <div className="text-6xl mb-4">🐱</div>
            <p className="text-gray-400 font-medium">Nenhum gato encontrado</p>
            <button
              onClick={() => router.push("/gatos/novo")}
              className="mt-4 bg-brand-500 text-white px-6 py-3 rounded-xl font-bold hover:bg-brand-600 transition-all"
            >
              Cadastrar primeiro gato
            </button>
          </div>
        ) : (
          <div className="space-y-3">
            {gatos.map((gato) => (
              <button
                key={gato.id}
                onClick={() => router.push(`/gatos/${gato.id}`)}
                className="w-full bg-white rounded-2xl p-4 shadow-premium border border-white flex items-center gap-4 premium-card text-left"
              >
                <div className="w-14 h-14 bg-brand-50 rounded-xl flex items-center justify-center text-2xl flex-shrink-0">
                  {gato.sexo === "M" ? "🐱" : "🐱"}
                </div>
                <div className="flex-1 min-w-0">
                  <h3 className="font-bold text-gray-800 truncate">{gato.nome}</h3>
                  <div className="flex items-center gap-2 mt-1">
                    {gato.raca && (
                      <span className="text-xs text-gray-400">{gato.raca}</span>
                    )}
                    {gato.idade && (
                      <span className="text-xs text-gray-400">• {gato.idade}</span>
                    )}
                  </div>
                </div>
                <span className={`text-[10px] font-bold px-2.5 py-1 rounded-full ${statusColors[gato.status] || "bg-gray-100 text-gray-600"}`}>
                  {gato.status}
                </span>
              </button>
            ))}
          </div>
        )}

        {/* Pagination */}
        {totalPages > 1 && (
          <div className="flex justify-center gap-2 pb-4">
            <button
              onClick={() => setPage((p) => Math.max(1, p - 1))}
              disabled={page === 1}
              className="px-4 py-2 rounded-xl bg-white border border-gray-200 disabled:opacity-50 font-bold text-sm"
            >
              Anterior
            </button>
            <span className="px-4 py-2 text-sm text-gray-500">
              {page} / {totalPages}
            </span>
            <button
              onClick={() => setPage((p) => Math.min(totalPages, p + 1))}
              disabled={page === totalPages}
              className="px-4 py-2 rounded-xl bg-white border border-gray-200 disabled:opacity-50 font-bold text-sm"
            >
              Próxima
            </button>
          </div>
        )}
      </div>
    </DashboardLayout>
  );
}
