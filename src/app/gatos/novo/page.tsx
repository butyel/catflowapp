"use client";

import { useState, useEffect, FormEvent } from "react";
import { useRouter } from "next/navigation";
import DashboardLayout from "@/components/DashboardLayout";

type Ala = { id: number; nome: string };

export default function NovoGatoPage() {
  const router = useRouter();
  const [alas, setAlas] = useState<Ala[]>([]);
  const [loading, setLoading] = useState(false);

  const [form, setForm] = useState({
    nome: "",
    sexo: "M",
    raca: "",
    dataNascimento: "",
    corPadrao: "",
    microchip: "",
    pedigree: "",
    castrado: false,
    peso: "",
    idade: "",
    status: "ativo",
    ala_id: "",
    doencasPreExistentes: "",
    historico: "",
  });

  useEffect(() => {
    fetch("/api/alas")
      .then((r) => r.json())
      .then((json) => {
        if (json.success) setAlas(json.data);
      })
      .catch(() => {});
  }, []);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setLoading(true);

    try {
      const res = await fetch("/api/gatos", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(form),
      });

      const json = await res.json();

      if (json.success) {
        router.push(`/gatos/${json.data.id}`);
      } else {
        alert(json.message || "Erro ao cadastrar");
      }
    } catch {
      alert("Erro de conexão");
    } finally {
      setLoading(false);
    }
  }

  function update(field: string, value: any) {
    setForm((prev) => ({ ...prev, [field]: value }));
  }

  return (
    <DashboardLayout title="Novo Gato 🐱">
      <div className="space-y-6 -mt-10">
        <form onSubmit={handleSubmit} className="bg-white rounded-[2rem] shadow-premium p-6 border border-white space-y-5">
          <div className="grid grid-cols-2 gap-4">
            <div className="col-span-2">
              <label className="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
              <input type="text" value={form.nome} onChange={(e) => update("nome", e.target.value)} required
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition-all" />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Sexo</label>
              <select value={form.sexo} onChange={(e) => update("sexo", e.target.value)}
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none transition-all">
                <option value="M">Macho</option>
                <option value="F">Fêmea</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Status</label>
              <select value={form.status} onChange={(e) => update("status", e.target.value)}
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none transition-all">
                <option value="ativo">Ativo</option>
                <option value="tratamento">Tratamento</option>
                <option value="adotado">Adotado</option>
                <option value="Doado">Doado</option>
                <option value="aposentado">Aposentado</option>
                <option value="obito">Óbito</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Raça</label>
              <input type="text" value={form.raca} onChange={(e) => update("raca", e.target.value)}
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none transition-all" />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Data de Nascimento</label>
              <input type="date" value={form.dataNascimento} onChange={(e) => update("dataNascimento", e.target.value)}
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none transition-all" />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Cor Padrão</label>
              <input type="text" value={form.corPadrao} onChange={(e) => update("corPadrao", e.target.value)}
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none transition-all" />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Peso (kg)</label>
              <input type="number" step="0.01" value={form.peso} onChange={(e) => update("peso", e.target.value)}
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none transition-all" />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Idade (ex: 2a 3m)</label>
              <input type="text" value={form.idade} onChange={(e) => update("idade", e.target.value)}
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none transition-all" />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Microchip</label>
              <input type="text" value={form.microchip} onChange={(e) => update("microchip", e.target.value)}
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none transition-all" />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Pedigree</label>
              <input type="text" value={form.pedigree} onChange={(e) => update("pedigree", e.target.value)}
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none transition-all" />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Ala</label>
              <select value={form.ala_id} onChange={(e) => update("ala_id", e.target.value)}
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none transition-all">
                <option value="">Nenhuma</option>
                {alas.map((ala) => (
                  <option key={ala.id} value={ala.id}>{ala.nome}</option>
                ))}
              </select>
            </div>

            <div className="col-span-2">
              <label className="flex items-center gap-3">
                <input type="checkbox" checked={form.castrado} onChange={(e) => update("castrado", e.target.checked)}
                  className="w-5 h-5 rounded border-gray-300 text-brand-500 focus:ring-brand-500" />
                <span className="text-sm font-medium text-gray-700">Castrado</span>
              </label>
            </div>

            <div className="col-span-2">
              <label className="block text-sm font-medium text-gray-700 mb-1">Doenças Pré-existentes</label>
              <textarea value={form.doencasPreExistentes} onChange={(e) => update("doencasPreExistentes", e.target.value)} rows={3}
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none transition-all" />
            </div>

            <div className="col-span-2">
              <label className="block text-sm font-medium text-gray-700 mb-1">Histórico</label>
              <textarea value={form.historico} onChange={(e) => update("historico", e.target.value)} rows={3}
                className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-brand-500 outline-none transition-all" />
            </div>
          </div>

          <div className="flex gap-3 pt-4">
            <button type="button" onClick={() => router.back()}
              className="flex-1 bg-gray-100 text-gray-600 font-bold py-3 rounded-xl hover:bg-gray-200 transition-all">
              Cancelar
            </button>
            <button type="submit" disabled={loading}
              className="flex-1 bg-brand-500 text-white font-bold py-3 rounded-xl hover:bg-brand-600 transition-all disabled:opacity-50">
              {loading ? "Salvando..." : "Salvar"}
            </button>
          </div>
        </form>
      </div>
    </DashboardLayout>
  );
}
