import { NextResponse } from "next/server";
import { getSession } from "@/lib/auth";
import { prisma } from "@/lib/db";

export async function GET() {
  const session = await getSession();
  if (!session) {
    return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });
  }

  const userId = session.userId;
  const isAdmin = session.userRole === "admin";
  const where = isAdmin ? {} : { userId };

  try {
    const [
      totalGatos,
      gatosTratamento,
      medicamentosAtivos,
      estoqueBaixo,
      lembreteCount,
      financeiroMes,
      vacinasPendentes,
    ] = await Promise.all([
      prisma.gato.count({ where: isAdmin ? {} : { userId, status: { not: "obito" } } }),
      prisma.gato.count({ where: isAdmin ? { status: "tratamento" } : { userId, status: "tratamento" } }),
      prisma.medicamento.count({ where: isAdmin ? { status: "ativo" } : { gato: { userId }, status: "ativo" } }),
      prisma.$queryRawUnsafe<Array<{ count: bigint }>>(
        `SELECT COUNT(*) as count FROM estoque WHERE ${isAdmin ? "1=1" : "user_id = ?"} AND quantidade_atual <= estoque_minimo`,
        ...(isAdmin ? [] : [userId])
      ).then((r) => Number(r[0]?.count || 0)),
      prisma.lembrete.count({ where: { ...where, status: "pendente" } }),
      getFinanceiroMes(userId, isAdmin),
      prisma.saude.count({
        where: {
          gato: isAdmin ? {} : { userId },
          tipo: "vacina",
          proximaData: { lte: new Date() },
        },
      }),
    ]);

    return NextResponse.json({
      success: true,
      data: {
        totalGatos,
        gatosTratamento,
        medicamentosAtivos: medicamentosAtivos,
        estoqueBaixo,
        vacinasPendentes,
        alerts: [],
        totalReceitas: financeiroMes.receitas,
        totalDespesas: financeiroMes.despesas,
      },
    });
  } catch (error) {
    console.error("Dashboard stats error:", error);
    return NextResponse.json(
      { success: false, message: "Erro ao carregar estatísticas" },
      { status: 500 }
    );
  }
}

async function getFinanceiroMes(userId: number, isAdmin: boolean) {
  const now = new Date();
  const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
  const startOfNextMonth = new Date(now.getFullYear(), now.getMonth() + 1, 1);

  const where = {
    ...(isAdmin ? {} : { userId }),
    data: { gte: startOfMonth, lt: startOfNextMonth },
  };

  const entries = await prisma.financeiro.findMany({ where });

  const receitas = entries
    .filter((e) => e.tipo === "receita")
    .reduce((sum, e) => sum + Number(e.valor), 0);

  const despesas = entries
    .filter((e) => e.tipo === "despesa")
    .reduce((sum, e) => sum + Number(e.valor), 0);

  return { receitas, despesas };
}
