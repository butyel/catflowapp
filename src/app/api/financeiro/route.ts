import { NextResponse } from "next/server";
import { getSession } from "@/lib/auth";
import { prisma } from "@/lib/db";

export async function GET(request: Request) {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  const { searchParams } = new URL(request.url);
  const mes = searchParams.get("mes") ? parseInt(searchParams.get("mes")!) : new Date().getMonth() + 1;
  const ano = searchParams.get("ano") ? parseInt(searchParams.get("ano")!) : new Date().getFullYear();

  const isAdmin = session.userRole === "admin";
  const where: any = {};

  if (!isAdmin) where.userId = session.userId;

  const startDate = new Date(ano, mes - 1, 1);
  const endDate = new Date(ano, mes, 1);
  where.data = { gte: startDate, lt: endDate };

  try {
    const [entries, receitas, despesas] = await Promise.all([
      prisma.financeiro.findMany({
        where,
        include: { item: { select: { nome: true } } },
        orderBy: { data: "desc" },
      }),
      prisma.financeiro.aggregate({
        where: { ...where, tipo: "receita" },
        _sum: { valor: true },
      }),
      prisma.financeiro.aggregate({
        where: { ...where, tipo: "despesa" },
        _sum: { valor: true },
      }),
    ]);

    const data = entries.map((e) => ({
      ...e,
      valor: Number(e.valor),
      quantidade: Number(e.quantidade),
      itemNome: e.item?.nome || null,
      item: undefined,
    }));

    return NextResponse.json({
      success: true,
      data,
      totalReceitas: Number(receitas._sum.valor || 0),
      totalDespesas: Number(despesas._sum.valor || 0),
    });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao buscar financeiro" }, { status: 500 });
  }
}

export async function POST(request: Request) {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  try {
    const body = await request.json();
    const entry = await prisma.financeiro.create({
      data: {
        userId: session.userId,
        tipo: body.tipo,
        descricao: body.descricao,
        categoria: body.categoria,
        valor: parseFloat(body.valor),
        quantidade: parseFloat(body.quantidade) || 1,
        data: new Date(body.data),
        itemId: body.itemId ? parseInt(body.itemId) : null,
      },
    });

    return NextResponse.json({ success: true, data: entry, message: "Lançamento criado!" });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao criar lançamento" }, { status: 500 });
  }
}
