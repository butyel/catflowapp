"use client";

import { useState, useEffect } from "react";
import DashboardLayout from "@/components/DashboardLayout";

export default function UsuariosPage() {
  const [loading] = useState(true);

  return (
    <DashboardLayout title="Usuários 👥">
      <div className="space-y-6 -mt-10">
        <div className="bg-white rounded-[2rem] shadow-premium p-6 border border-white">
          <p className="text-center text-gray-400 py-8">
            Gerenciamento de usuários disponível apenas para administradores.
          </p>
        </div>
      </div>
    </DashboardLayout>
  );
}
