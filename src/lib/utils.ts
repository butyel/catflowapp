export function formatCurrency(value: number | string): string {
  const num = typeof value === "string" ? parseFloat(value) : value;
  return num.toLocaleString("pt-BR", {
    style: "currency",
    currency: "BRL",
  });
}

export function formatDate(date: Date | string): string {
  const d = typeof date === "string" ? new Date(date) : date;
  return d.toLocaleDateString("pt-BR");
}

export function formatDateTime(date: Date | string): string {
  const d = typeof date === "string" ? new Date(date) : date;
  return d.toLocaleString("pt-BR");
}

export function calculateAge(dataNascimento: Date | string): string {
  const nasc = typeof dataNascimento === "string" ? new Date(dataNascimento) : dataNascimento;
  const hoje = new Date();
  let anos = hoje.getFullYear() - nasc.getFullYear();
  let meses = hoje.getMonth() - nasc.getMonth();
  if (meses < 0) {
    anos--;
    meses += 12;
  }
  if (anos > 0) return `${anos}a ${meses}m`;
  return `${meses}m`;
}

export function cn(...classes: (string | boolean | undefined | null)[]): string {
  return classes.filter(Boolean).join(" ");
}

export function sanitize(str: string): string {
  return str.replace(/[<>&"']/g, (char) => {
    const entities: Record<string, string> = {
      "<": "&lt;",
      ">": "&gt;",
      "&": "&amp;",
      '"': "&quot;",
      "'": "&#39;",
    };
    return entities[char] || char;
  });
}

export function getStatusColor(status: string): string {
  const colors: Record<string, string> = {
    ativo: "bg-green-100 text-green-800",
    tratamento: "bg-yellow-100 text-yellow-800",
    adotado: "bg-blue-100 text-blue-800",
    Doado: "bg-purple-100 text-purple-800",
    aposentado: "bg-gray-100 text-gray-800",
    obito: "bg-red-100 text-red-800",
  };
  return colors[status] || "bg-gray-100 text-gray-800";
}
