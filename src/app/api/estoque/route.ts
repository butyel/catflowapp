import { NextResponse } from "next/server";
import { getSession } from "@/lib/auth";
import { prisma } from "@/lib/db";

export async function GET() {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  const isAdmin = session.userRole === "admin";
  const where = isAdmin ? {} : { userId: session.userId };

  try {
    const items = await prisma.estoque.findMany({
      where,
      orderBy: { nomeItem: "asc" },
    });

    const data = items.map((i) => ({
      ...i,
      quantidadeAtual: Number(i.quantidadeAtual),
      estoqueMinimo: Number(i.estoqueMinimo),
      baixoEstoque: Number(i.quantidadeAtual) <= Number(i.estoqueMinimo),
    }));

    return NextResponse.json({ success: true, data });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao buscar estoque" }, { status: 500 });
  }
}

export async function POST(request: Request) {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  try {
    const body = await request.json();
    const item = await prisma.estoque.create({
      data: {
        userId: session.userId,
        nomeItem: body.nomeItem,
        categoria: body.categoria,
        quantidadeAtual: parseFloat(body.quantidadeAtual) || 0,
        unidade: body.unidade || "un",
        estoqueMinimo: parseFloat(body.estoqueMinimo) || 0,
      },
    });

    return NextResponse.json({ success: true, data: item, message: "Item adicionado!" });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao adicionar item" }, { status: 500 });
  }
}
