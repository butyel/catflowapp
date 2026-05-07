"use client";

import { useState, useEffect } from "react";
import DashboardLayout from "@/components/DashboardLayout";

export default function PerfilPage() {
  const [user, setUser] = useState<any>(null);

  useEffect(() => {
    fetch("/api/auth/me")
      .then((r) => r.json())
      .then((data) => { if (data.authenticated) setUser(data.user); });
  }, []);

  return (
    <DashboardLayout title="Meu Perfil 👤">
      <div className="space-y-6 -mt-10">
        <div className="bg-white rounded-[2rem] shadow-premium p-6 border border-white">
          <div className="flex items-center gap-4 mb-6">
            <div className="w-20 h-20 bg-brand-50 rounded-2xl flex items-center justify-center text-4xl">
              {user?.nome?.[0]?.toUpperCase() || "?"}
            </div>
            <div>
              <h2 className="text-2xl font-outfit font-extrabold text-gray-800">{user?.nome || "Carregando..."}</h2>
              <p className="text-sm text-gray-500 capitalize">{user?.role === "admin" ? "Administrador" : "Tutor"}</p>
            </div>
          </div>

          <div className="space-y-4">
            <InfoRow label="Email" value={user?.id ? "usuario@email.com" : "-"} />
            <InfoRow label="Membro desde" value="-" />
            <InfoRow label="Tipo de conta" value={user?.role === "admin" ? "Administrador" : "Tutor"} />
          </div>
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
