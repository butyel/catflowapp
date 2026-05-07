import { NextResponse } from "next/server";
import { getSession } from "@/lib/auth";
import { prisma } from "@/lib/db";

export async function GET(request: Request) {
  const session = await getSession();
  if (!session) {
    return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });
  }

  const { searchParams } = new URL(request.url);
  const page = Math.max(1, parseInt(searchParams.get("page") || "1"));
  const search = searchParams.get("search") || "";
  const limit = 20;
  const offset = (page - 1) * limit;

  const userId = session.userId;
  const isAdmin = session.userRole === "admin";

  try {
    const where: any = {};
    if (!isAdmin) where.userId = userId;
    if (search) {
      where.OR = [
        { nome: { contains: search } },
        { raca: { contains: search } },
        { corPadrao: { contains: search } },
      ];
    }

    const [gatos, total] = await Promise.all([
      prisma.gato.findMany({
        where,
        include: {
          ala: { select: { nome: true } },
          user: isAdmin ? { select: { nome: true } } : false,
        },
        orderBy: { createdAt: "desc" },
        take: limit,
        skip: offset,
      }),
      prisma.gato.count({ where }),
    ]);

    const data = gatos.map((g) => ({
      ...g,
      peso: g.peso ? Number(g.peso) : null,
      ala_nome: g.ala?.nome || null,
      tutor_nome: isAdmin ? (g.user as any)?.nome : undefined,
      ala: undefined,
      user: undefined,
    }));

    return NextResponse.json({
      success: true,
      data,
      pagination: {
        current_page: page,
        total_pages: Math.ceil(total / limit),
        total_items: total,
        per_page: limit,
      },
    });
  } catch (error) {
    console.error("List gatos error:", error);
    return NextResponse.json({ success: false, message: "Erro ao buscar gatos" }, { status: 500 });
  }
}

export async function POST(request: Request) {
  const session = await getSession();
  if (!session) {
    return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });
  }

  try {
    const body = await request.json();
    const userId = session.userId;

    if (!body.nome?.trim()) {
      return NextResponse.json({ success: false, message: "Nome do gato é obrigatório" }, { status: 400 });
    }

    if (body.ala_id) {
      const ala = await prisma.ala.findFirst({
        where: { id: body.ala_id, userId },
      });
      if (!ala) {
        return NextResponse.json({ success: false, message: "Ala não encontrada" }, { status: 400 });
      }
    }

    const gato = await prisma.gato.create({
      data: {
        userId,
        nome: body.nome,
        sexo: body.sexo || "M",
        status: body.status || "ativo",
        castrado: body.castrado || false,
        idade: body.idade || null,
        peso: body.peso ? parseFloat(body.peso) : null,
        raca: body.raca || null,
        dataNascimento: body.dataNascimento ? new Date(body.dataNascimento) : null,
        corPadrao: body.corPadrao || null,
        microchip: body.microchip || null,
        pedigree: body.pedigree || null,
        alaId: body.ala_id ? parseInt(body.ala_id) : null,
        doencasPreExistentes: body.doencasPreExistentes || null,
        historico: body.historico || null,
      },
    });

    return NextResponse.json({ success: true, data: { id: gato.id }, message: "Gato cadastrado com sucesso!" });
  } catch (error) {
    console.error("Create gato error:", error);
    return NextResponse.json({ success: false, message: "Erro interno ao salvar" }, { status: 500 });
  }
}
