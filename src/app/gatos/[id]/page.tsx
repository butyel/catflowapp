"use client";

import { useState, useEffect, useRef } from "react";
import { useRouter, useParams } from "next/navigation";
import DashboardLayout from "@/components/DashboardLayout";

type GatoProfile = {
  id: number;
  nome: string;
  foto: string | null;
  sexo: string;
  raca: string | null;
  dataNascimento: string | null;
  corPadrao: string | null;
  microchip: string | null;
  pedigree: string | null;
  castrado: boolean;
  peso: number | null;
  idade: string | null;
  status: string;
  personalidade: string | null;
  doencasPreExistentes: string | null;
  historico: string | null;
  ala: { id: number; nome: string } | null;
  saude: Array<{
    id: number;
    tipo: string;
    descricao: string;
    dataEvento: string;
    proximaData: string | null;
  }>;
  medicamentos: Array<{
    id: number;
    nomeMedicamento: string;
    dosagem: string;
    horario: string;
    status: string;
  }>;
  pesoHistorico: Array<{ id: number; peso: number; data: string }>;
  adocoes: Array<{
    id: number;
    adotanteNome: string;
    contato: string;
    dataAdocao: string;
    observacoes: string | null;
  }>;
  galeria: Array<{
    id: number;
    fotoPath: string;
    legenda: string | null;
    data: string;
  }>;
};

export default function PerfilGatoPage() {
  const router = useRouter();
  const params = useParams();
  const [gato, setGato] = useState<GatoProfile | null>(null);
  const [loading, setLoading] = useState(true);
  const [activeTab, setActiveTab] = useState<"info" | "saude" | "med" | "galeria" | "historico">("info");

  async function loadGato() {
    try {
      const res = await fetch(`/api/gatos/${params.id}`);
      const json = await res.json();
      if (json.success) setGato(json.data);
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadGato();
  }, [params.id]);

  async function handlePhotoUpload(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;

    const formData = new FormData();
    formData.append("file", file);
    formData.append("tipo", "gatos");

    try {
      const res = await fetch("/api/upload", { method: "POST", body: formData });
      const json = await res.json();
      if (json.success) {
        await fetch(`/api/gatos/${params.id}`, {
          method: "PUT",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ ...gato, foto: json.data.url }),
        });
        setGato((prev) => (prev ? { ...prev, foto: json.data.url } : prev));
      }
    } catch (e) {
      console.error(e);
    }
  }

  async function handleGaleriaUpload(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;

    const formData = new FormData();
    formData.append("file", file);
    formData.append("tipo", "gatos");

    try {
      const res = await fetch("/api/upload", { method: "POST", body: formData });
      const json = await res.json();
      if (json.success) {
        await fetch("/api/galeria", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ gatoId: gato?.id, fotoPath: json.data.url, data: new Date().toISOString().split("T")[0] }),
        });
        loadGato();
      }
    } catch (e) {
      console.error(e);
    }
  }

  async function handleDelete() {
    if (!confirm("Tem certeza que deseja remover este gato?")) return;
    try {
      const res = await fetch(`/api/gatos/${params.id}`, { method: "DELETE" });
      const json = await res.json();
      if (json.success) {
        router.push("/gatos");
      }
    } catch (e) {
      console.error(e);
    }
  }

  if (loading) {
    return (
      <DashboardLayout>
        <div className="space-y-6 -mt-10">
          <div className="shimmer h-48 rounded-[2rem]" />
          <div className="shimmer h-64 rounded-[2rem]" />
        </div>
      </DashboardLayout>
    );
  }

  if (!gato) {
    return (
      <DashboardLayout>
        <div className="text-center py-16 -mt-10">
          <p className="text-gray-400">Gato não encontrado</p>
        </div>
      </DashboardLayout>
    );
  }

  const tabs = [
    { key: "info", label: "Informações" },
    { key: "saude", label: "Saúde" },
    { key: "med", label: "Medicamentos" },
    { key: "galeria", label: "Fotos" },
    { key: "historico", label: "Histórico" },
  ] as const;

  function formatDate(d: string | null) {
    if (!d) return "-";
    return new Date(d).toLocaleDateString("pt-BR");
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
    <DashboardLayout>
      <div className="space-y-6 -mt-10">
        {/* Profile Header */}
        <div className="bg-white rounded-[2rem] shadow-premium p-6 border border-white">
          <div className="flex items-center gap-4 mb-4">
            <div className="relative">
              <div className="w-20 h-20 bg-brand-50 rounded-2xl flex items-center justify-center text-4xl overflow-hidden">
                {gato.foto ? (
                  <img src={gato.foto} alt={gato.nome} className="w-full h-full object-cover" />
                ) : (
                  gato.sexo === "M" ? "🐱" : "🐱"
                )}
              </div>
              <button onClick={() => document.getElementById("foto-upload")?.click()}
                className="absolute -bottom-1 -right-1 w-7 h-7 bg-brand-500 text-white rounded-full flex items-center justify-center text-sm shadow-md hover:bg-brand-600 transition-colors">
                +
              </button>
              <input id="foto-upload" type="file" accept="image/*" className="hidden" onChange={handlePhotoUpload} />
            </div>
            <div className="flex-1">
              <h2 className="text-2xl font-outfit font-extrabold text-gray-800">{gato.nome}</h2>
              <div className="flex items-center gap-2 mt-1">
                {gato.raca && <span className="text-sm text-gray-500">{gato.raca}</span>}
                <span className={`text-[10px] font-bold px-2.5 py-1 rounded-full ${statusColors[gato.status]}`}>
                  {gato.status}
                </span>
              </div>
            </div>
            <button onClick={handleDelete} className="p-2 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition-all">
              <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
              </svg>
            </button>
          </div>

          <div className="grid grid-cols-3 gap-3 text-center">
            <div className="bg-gray-50 rounded-xl p-3">
              <p className="text-lg font-extrabold text-brand-600">{gato.idade || "-"}</p>
              <p className="text-[9px] text-gray-400 font-bold uppercase">Idade</p>
            </div>
            <div className="bg-gray-50 rounded-xl p-3">
              <p className="text-lg font-extrabold text-brand-600">{gato.peso ? `${gato.peso}kg` : "-"}</p>
              <p className="text-[9px] text-gray-400 font-bold uppercase">Peso</p>
            </div>
            <div className="bg-gray-50 rounded-xl p-3">
              <p className="text-lg font-extrabold text-brand-600">{gato.castrado ? "Sim" : "Não"}</p>
              <p className="text-[9px] text-gray-400 font-bold uppercase">Castrado</p>
            </div>
          </div>
        </div>

        {/* Tabs */}
        <div className="flex bg-white rounded-2xl p-1 shadow-sm border border-gray-100 overflow-x-auto">
          {tabs.map((tab) => (
            <button
              key={tab.key}
              onClick={() => setActiveTab(tab.key)}
              className={`flex-1 py-2.5 px-4 rounded-xl text-sm font-bold transition-all ${
                activeTab === tab.key
                  ? "bg-brand-500 text-white shadow-md"
                  : "text-gray-500 hover:text-gray-700"
              }`}
            >
              {tab.label}
            </button>
          ))}
        </div>

        {/* Tab Content */}
        <div className="bg-white rounded-[2rem] shadow-premium p-6 border border-white">
          {activeTab === "info" && (
            <div className="space-y-4">
              <InfoRow label="Sexo" value={gato.sexo === "M" ? "Macho" : "Fêmea"} />
              <InfoRow label="Cor" value={gato.corPadrao || "-"} />
              <InfoRow label="Data Nascimento" value={formatDate(gato.dataNascimento)} />
              <InfoRow label="Microchip" value={gato.microchip || "-"} />
              <InfoRow label="Pedigree" value={gato.pedigree || "-"} />
              <InfoRow label="Ala" value={gato.ala?.nome || "-"} />
            </div>
          )}

          {activeTab === "saude" && (
            <div className="space-y-4">
              {gato.saude.length === 0 ? (
                <p className="text-center text-gray-400 py-8">Nenhum registro de saúde</p>
              ) : (
                gato.saude.map((s) => (
                  <div key={s.id} className="bg-gray-50 rounded-xl p-4">
                    <div className="flex justify-between items-start">
                      <div>
                        <p className="font-bold text-gray-800">{s.descricao}</p>
                        <p className="text-xs text-gray-500 capitalize">{s.tipo}</p>
                      </div>
                      <div className="text-right text-xs text-gray-400">
                        <p>{formatDate(s.dataEvento)}</p>
                        {s.proximaData && (
                          <p className="text-brand-600 font-bold">Próxima: {formatDate(s.proximaData)}</p>
                        )}
                      </div>
                    </div>
                  </div>
                ))
              )}
            </div>
          )}

          {activeTab === "med" && (
            <div className="space-y-4">
              {gato.medicamentos.length === 0 ? (
                <p className="text-center text-gray-400 py-8">Nenhum medicamento ativo</p>
              ) : (
                gato.medicamentos.map((m) => (
                  <div key={m.id} className="bg-gray-50 rounded-xl p-4">
                    <div className="flex justify-between items-start">
                      <div>
                        <p className="font-bold text-gray-800">{m.nomeMedicamento}</p>
                        <p className="text-xs text-gray-500">{m.dosagem}</p>
                      </div>
                      <div className="text-right">
                        <span className="text-[10px] font-bold px-2 py-1 rounded-full bg-green-100 text-green-700">
                          {m.horario}
                        </span>
                      </div>
                    </div>
                  </div>
                ))
              )}
            </div>
          )}

          {activeTab === "galeria" && (
            <div className="space-y-4">
              <button onClick={() => document.getElementById("galeria-upload")?.click()}
                className="bg-brand-500 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-brand-600 transition-all">
                + Adicionar Foto
              </button>
              <input id="galeria-upload" type="file" accept="image/*" className="hidden" onChange={handleGaleriaUpload} />
              {gato.galeria && gato.galeria.length > 0 ? (
                <div className="grid grid-cols-3 gap-2">
                  {gato.galeria.map((foto) => (
                    <div key={foto.id} className="aspect-square bg-gray-100 rounded-xl overflow-hidden">
                      <img src={foto.fotoPath} alt={foto.legenda || ""} className="w-full h-full object-cover" />
                    </div>
                  ))}
                </div>
              ) : (
                <p className="text-center text-gray-400 py-8">Nenhuma foto na galeria</p>
              )}
            </div>
          )}

          {activeTab === "historico" && (
            <div className="space-y-4">
              {gato.doencasPreExistentes && (
                <div>
                  <h3 className="text-sm font-bold text-gray-500 uppercase mb-2">Doenças Pré-existentes</h3>
                  <p className="text-gray-700 text-sm">{gato.doencasPreExistentes}</p>
                </div>
              )}
              {gato.historico && (
                <div>
                  <h3 className="text-sm font-bold text-gray-500 uppercase mb-2">Histórico</h3>
                  <p className="text-gray-700 text-sm whitespace-pre-wrap">{gato.historico}</p>
                </div>
              )}
              {!gato.doencasPreExistentes && !gato.historico && (
                <p className="text-center text-gray-400 py-8">Nenhum histórico registrado</p>
              )}
            </div>
          )}
        </div>
      </div>
    </DashboardLayout>
  );
}

function InfoRow({ label, value }: { label: string; value: string }) {
  return (
    <div className="flex justify-between items-center py-2 border-b border-gray-50 last:border-0">
      <span className="text-sm text-gray-500">{label}</span>
      <span className="text-sm font-semibold text-gray-800">{value}</span>
    </div>
  );
}
